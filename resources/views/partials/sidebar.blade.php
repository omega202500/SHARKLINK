<aside class="hidden min-h-screen w-[270px] shrink-0 flex-col bg-navy px-3.5 py-6 lg:flex">
    <div class="flex items-center justify-between px-2 pb-[22px]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-[3px]">
            <img src="{{ asset('images/shark-mark.svg') }}" alt="" class="h-[31px] w-[29px]">
            <span class="text-[21px] font-extrabold text-white">SHARKLINK</span>
        </a>
        <button type="button" class="text-white" aria-label="Réduire le menu">
            <x-icon name="panel-left-close" :size="16" />
        </button>
    </div>

    <nav class="flex flex-col">
        @foreach ($navigation as $entry)
            @if ($entry['type'] === 'heading')
                <div class="flex items-start justify-between px-3 pb-2.5 pt-[22px]">
                    <p class="text-sm font-bold text-white">{{ $entry['title'] }}</p>
                    <a href="#" class="text-[10px] text-navy-soft">{{ $entry['action'] }}</a>
                </div>
            @else
                <a href="{{ $entry['href'] ?? '#' }}"
                   @class([
                       'flex items-center gap-[13px] rounded-[5px] py-[13px] text-[13px]',
                       'px-[22px]' => ! empty($entry['indent']),
                       'px-3' => empty($entry['indent']),
                       'bg-white font-bold text-navy' => ! empty($entry['active']),
                       'font-medium text-white' => empty($entry['active']),
                   ])
                   @if (! empty($entry['active'])) aria-current="page" @endif>
                    <x-icon :name="$entry['icon']" />
                    <span class="min-w-0 flex-1">{{ $entry['label'] }}</span>
                    @if (! empty($entry['chevron']))
                        <x-icon name="chevron-down" :size="12" />
                    @endif
                </a>
            @endif
        @endforeach
    </nav>
</aside>
