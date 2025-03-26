 @if(!empty($log['stack_trace']))
 <tr>
    <td colspan="4" class="px-0">
       <pre style="white-space: pre-wrap; word-break: break-word; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">{{ $log['stack_trace'] }}</pre>
    </td>
 </tr>
 @endif

 