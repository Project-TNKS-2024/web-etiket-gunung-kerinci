@foreach ($sidebar as $item)
@if ($item['type'] == "single")
<li class="sidebar-item">
   <a class="sidebar-link " href="{{$item['link']}}" aria-expanded="false">
      <span>
         @if($item['icon']['type'] == "bootstrap")
         <i class="{{$item['icon']['name']}}"></i>
         @elseif ($item['icon']['type'] == "image")
         <img src="{{$item['icon']['name']}}"></img>
         @endif
      </span>
      <span class="hide-menu">{{$item['name']}}</span>
   </a>
</li>
@endif

@if ($item['type'] == "multiple")
<li class="nav-small-cap">
   <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
   <span class="hide-menu">{{$item['name']}}</span>
</li>
@foreach ($item['list'] as $list)
<li class="sidebar-item">
   <a class="sidebar-link" href="{{$list['link']}}" aria-expanded="false">
      <span>
         @if($list['icon']['type'] == "bootstrap")
         <i class="{{$list['icon']['name']}}"></i>
         @elseif ($list['icon']['type'] == "image")
         <img src="{{$list['icon']['name']}}"></img>
         @endif
      </span>
      <span class="hide-menu">{{$list['name']}}</span>
   </a>
</li>
@endforeach
@endif
@endforeach