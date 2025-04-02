<?php

namespace App\Http\Controllers\etiket\admin\fitur;

use App\Http\Controllers\Controller;
use App\Models\Event;
use DateTime;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('b', date('m-Y')); // Default bulan sekarang
        $date = DateTime::createFromFormat('m-Y', $bulan);

        if (!$date) {
            abort(400, 'Format bulan tidak valid'); // Handle error jika format salah
        }

        // Ambil bulan & tahun sekarang
        $startYear = $date->format('Y');
        $startMonth = $date->format('m');

        // Hitung bulan berikutnya
        $nextMonth = (int) $startMonth + 1;
        $nextYear = $startYear;

        if ($nextMonth > 12) {
            $nextMonth = 1; // Jika bulan Desember, lompat ke Januari
            $nextYear += 1;
        }

        // Format ulang bulan berikutnya agar selalu 2 digit
        $nextMonth = str_pad($nextMonth, 2, '0', STR_PAD_LEFT);

        // Ambil event dengan rentang 2 bulan dari `start_date`
        $events = Event::whereBetween('start_date', [
            "$startYear-$startMonth-01",
            "$nextYear-$nextMonth-31"
        ])->get();

        return view('etiket.admin.fitur.kalender.index', [
            'events' => $events,
            'bulan' => $date->format('m-Y'), // Pastikan selalu dalam format yang benar
        ]);
    }


    public function storeEvent(Request $request)
    {
        $request->validate([
            'eventStartDate' => 'required|date',
            'eventEndDate' => 'required|date|after_or_equal:eventStartDate',
            'title' => 'required|string',
            'is_holiday' => 'nullable|integer',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'start_date' => $request->eventStartDate,
            'end_date' => $request->eventEndDate,
            'is_holiday' => $request->is_holiday ?? false,
        ]);

        return redirect()->back()->with('success', 'Event berhasil ditambahkan.');
    }

    public function storjsonEvent(Request $request)
    {
        // Pastikan file diunggah
        if (!$request->hasFile('jsonEventInput')) {
            return redirect()->back()->with('error', 'File JSON tidak ditemukan.');
        }

        // Ambil file dan baca isinya
        $file = $request->file('jsonEventInput');
        $jsonContent = file_get_contents($file->getPathname());
        $jsonData = json_decode($jsonContent, true);

        // Debugging: Periksa isi JSON
        if (!$jsonData) {
            return redirect()->back()->with('error', 'Format data JSON tidak valid atau kosong.');
        }

        // return $jsonData;
        // Jika hanya satu event, bungkus dalam array
        $events = is_array(reset($jsonData)) ? $jsonData : [$jsonData];

        foreach ($events as $eventData) {
            // Pastikan data wajib ada
            if (!isset($eventData['title'], $eventData['start_date'], $eventData['is_holiday'])) {
                session()->flash('error', 'Format data JSON tidak valid untuk beberapa event.');
                continue;
            }

            Event::create([
                'title' => $eventData['title'],
                'start_date' => $eventData['start_date'],
                'end_date' => $eventData['end_date'] ?? $eventData['start_date'], // Jika end_date kosong, gunakan start_date
                'is_holiday' => $eventData['is_holiday'],
            ]);

            session()->flash('success', 'Event ' . $eventData['title'] . 'berhasil ditambahkan.');
        }


        return redirect()->back();
    }

    public function destroyEvent(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:events,id',
        ]);
        Event::destroy($request->id);
        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }
}
