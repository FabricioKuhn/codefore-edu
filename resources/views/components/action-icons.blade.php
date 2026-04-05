@props(['item', 'resource'])

<td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
    @foreach($actions as $action)
        @if($action['type'] === 'link')
            <a href="{{ $action['route'] }}" 
               class="inline-block transition" 
               data-tooltip="{{ $action['tooltip'] }}"
               @if(isset($action['target'])) target="{{ $action['target'] }}" @endif>
                @include("components.icons.{$action['icon']}")
            </a>
        @elseif($action['type'] === 'form')
            <form action="{{ $action['route'] }}" method="POST" class="inline" 
                  @if(isset($action['confirm'])) onsubmit="return confirm('{{ $action['confirm'] }}')" @endif>
                @csrf
                @method($action['method'] ?? 'POST')
                <button type="submit" 
                        class="inline-block transition" 
                        data-tooltip="{{ $action['tooltip'] }}">
                    @include("components.icons.{$action['icon']}")
                </button>
            </form>
        @elseif($action['type'] === 'button')
            <button @click="{{ $action['action'] }}" 
                    class="inline-block transition" 
                    data-tooltip="{{ $action['tooltip'] }}">
                @include("components.icons.{$action['icon']}")
            </button>
        @endif
    @endforeach
</td>
