<?php

namespace App\Http\Controllers\etiket\admin\fitur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    protected $logFiles;
    public function __construct()
    {
        $this->logFiles = glob(storage_path('logs/*.log'));
    }

    public function index(Request $request)
    {
        $filename = $request->query('file', 'laravel.log');
        $filename = basename($filename);

        if (!str_ends_with($filename, '.log')) {
            return response()->json(['message' => 'Invalid log file'], 400);
        }

        $logFile = storage_path('logs/' . $filename);
        if (!File::exists($logFile)) {
            return response()->json(['message' => 'Log file not found'], 404);
        }

        $logs = File::get($logFile);
        $logEntries = $this->parseLogs($logs);

        $logCounts = [
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0,
        ];

        foreach ($logEntries as $log) {
            $level = strtolower($log['level']);
            if (isset($logCounts[$level])) {
                $logCounts[$level]++;
            }
        }

        return view('etiket.admin.fitur.log.index', [
            'logEntries' => $logEntries,
            'logFile' => $filename,
            'logFiles' => $this->logFiles,
            'logCounts' => $logCounts,
            'filename' => $filename,
            'getBadgeClass' => [$this, 'getBadgeClass'],
        ]);
    }

    public function getBadgeClass($level)
    {
        return match ($level) {
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            'debug' => 'secondary',
            default => 'primary',
        };
    }

    private function parseLogs($logs)
    {
        $logEntries = [];
        $currentEntry = null;

        foreach (explode("\n", trim($logs)) as $line) {
            $line = trim($line); // Hilangkan whitespace berlebih

            // Deteksi baris utama log (timestamp, channel, level, dan pesan utama)
            if (preg_match('/\[(.*?)\] ([\w\s]+?)\.(\w+): (.*)/', $line, $matches)) {
                if ($currentEntry) {
                    $logEntries[] = $currentEntry; // Simpan entry sebelumnya sebelum membuat yang baru
                }

                $currentEntry = [
                    'time' => trim($matches[1]),     // Waktu log
                    'channel' => trim($matches[2]),  // Context log (local, production, dll.)
                    'level' => strtolower(trim($matches[3])),  // Level log (error, info, dll.)
                    'message' => trim($matches[4]),  // Pesan utama error
                    'stack_trace' => [] // Siapkan array untuk stack trace
                ];
            }
            // Deteksi "[previous exception] [object] (Error..."
            elseif (preg_match('/^\[previous exception\] \[object\] \(([^)]+)\): (.*)/i', $line, $matches)) {
                if ($currentEntry) {
                    $logEntries[] = $currentEntry; // Simpan entry sebelumnya
                }

                $currentEntry = [
                    'time' => $logEntries ? end($logEntries)['time'] : '',      // Ambil waktu terakhir
                    'channel' => $logEntries ? end($logEntries)['channel'] : '',   // Ambil channel terakhir
                    'level' => $logEntries ? end($logEntries)['level'] : '',     // Ambil level terakhir
                    'message' => "Previous Exception ({$matches[1]}): " . trim($matches[2]),  // Format baru untuk previous exception
                    'stack_trace' => [] // Kosongkan stack trace (akan ditambahkan jika ada)
                ];
            }
            // Deteksi stack trace yang diawali dengan "#"
            elseif ($currentEntry && preg_match('/^#\d+\s+/', $line)) {
                $currentEntry['stack_trace'][] = $line; // Tambahkan stack trace ke dalam array
            }
        }

        // Simpan entry terakhir jika ada
        if ($currentEntry) {
            $logEntries[] = $currentEntry;
        }

        return array_reverse($logEntries);
    }
}
