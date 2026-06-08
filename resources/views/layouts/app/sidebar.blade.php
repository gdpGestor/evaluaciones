
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-slate-100">
        {{-- Menú lateral para computadora --}}
        <flux:sidebar
            sticky
            collapsible="mobile"
            class="border-e border-slate-200 bg-white"
        >
            <flux:sidebar.header>
                <x-app-logo
                    :sidebar="true"
                    href="{{ route('dashboard') }}"
                    wire:navigate
                />

                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            {{-- Navegación principal --}}
            <flux:sidebar.nav>
                {{-- Portal de evaluaciones --}}
                <flux:sidebar.group
                    heading="Portal de evaluaciones"
                    class="grid"
                >
                    <flux:sidebar.item
                        icon="home"
                        :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')"
                        wire:navigate
                    >
                        Mis evaluaciones
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="chart-bar"
                        :href="route('evaluaciones.resultados')"
                        :current="request()->routeIs('evaluaciones.resultados')"
                        wire:navigate
                    >
                        Mis resultados
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- Administración --}}
                @if (auth()->user()->es_admin)
                    <flux:sidebar.group
                        heading="Administración"
                        class="grid"
                    >
                        <flux:sidebar.item
                            icon="briefcase"
                            :href="route('administracion.puestos')"
                            :current="request()->routeIs('administracion.puestos')"
                            wire:navigate
                        >
                            Puestos
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="users"
                            :href="route('administracion.empleados')"
                            :current="request()->routeIs('administracion.empleados')"
                            wire:navigate
                        >
                            Empleados
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="arrows-right-left"
                            :href="route('administracion.relaciones-puestos')"
                            :current="request()->routeIs('administracion.relaciones-puestos')"
                            wire:navigate
                        >
                            Relaciones entre puestos
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="clipboard-document-check"
                            :href="route('administracion.seguimiento-evaluaciones')"
                            :current="request()->routeIs('administracion.seguimiento-evaluaciones')"
                            wire:navigate
                        >
                            Seguimiento de evaluaciones
                        </flux:sidebar.item>

                        <flux:sidebar.item
                            icon="document-text"
                            :href="route('administracion.plantillas')"
                            :current="request()->routeIs('administracion.plantillas')"
                            wire:navigate
                        >
                            Plantillas de evaluación
                        </flux:sidebar.item>

                    </flux:sidebar.group>
                @endif

                {{-- Mi cuenta --}}
               {{--  <flux:sidebar.group
                    heading="Mi cuenta"
                    class="grid"
                >
                    <flux:sidebar.item
                        icon="cog"
                        :href="route('profile.edit')"
                        :current="request()->routeIs('profile.edit')"
                        wire:navigate
                    >
                        Configuración
                    </flux:sidebar.item>
                </flux:sidebar.group> --}}
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- Menú del usuario en computadora --}}
            <x-desktop-user-menu
                class="hidden lg:block"
                :name="auth()->user()->name"
            />
        </flux:sidebar>

        {{-- Encabezado para teléfono y tablet --}}
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle
                class="lg:hidden"
                icon="bars-2"
                inset="left"
            />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    {{-- Datos del usuario --}}
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">
                                        {{ auth()->user()->name }}
                                    </flux:heading>

                                    <flux:text class="truncate">
                                        {{ auth()->user()->email }}
                                    </flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    {{-- Configuración --}}
                    <flux:menu.radio.group>
                        <flux:menu.item
                            :href="route('profile.edit')"
                            icon="cog"
                            wire:navigate
                        >
                            Configuración
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    {{-- Cerrar sesión --}}
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="w-full"
                    >
                        @csrf

                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            Cerrar sesión
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>