@props(['links'])

<nav class="flex text-sm font-medium text-gray-500 mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        @foreach($links as $index => $link)
            <li class="inline-flex items-center">
                @if(!$loop->last)
                    <a href="{{ $link['url'] }}" class="inline-flex items-center hover:text-[#00ad9a] transition-colors text-[#333333]">
                        {{ $link['name'] }}
                    </a>
                    <svg class="w-3 h-3 text-gray-400 mx-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                @else
                    <span class="text-gray-400">{{ $link['name'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>