<header class="flex h-16 shrink-0 items-center justify-between gap-3">
    <div class="flex min-w-0 flex-1 items-center gap-3 lg:flex-none">

        {{-- Bouton menu : à gauche, visible uniquement sous lg --}}
        <button type="button"
                id="sidebar-toggle"
                class="flex size-8 shrink-0 items-center justify-center text-ink lg:hidden"
                aria-label="Ouvrir le menu"
                aria-controls="sidebar"
                aria-expanded="false">
            <i id="sidebar-toggle-icon" class="fa-solid fa-bars text-xl"></i>
        </button>

        <label class="flex h-9 min-w-0 flex-1 items-center gap-2.5 rounded-[5px] bg-white px-3 lg:w-[330px] lg:flex-none">
            <x-icon name="search" :size="16" class="text-muted" />
            <input type="search" name="q" placeholder="Search"
                   class="w-full bg-transparent text-xs text-ink placeholder:text-muted focus:outline-none">
        </label>
    </div>

    <div class="flex items-center gap-5">
        <button type="button" class="relative h-[30px] w-[26px] text-ink" aria-label="Notifications">
            <x-icon name="bell" :size="23" class="absolute left-0 top-[3.5px]" />
            <span class="absolute right-0 top-px size-[9px] rounded-full bg-red-500"></span>
        </button>
        <button type="button" class="flex size-8 items-center justify-center rounded-full bg-navy text-white" aria-label="Mon compte">
            <x-icon name="user-round" :size="19" />
        </button>
    </div>
</header>