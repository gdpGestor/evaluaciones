
<div class="mx-auto w-full max-w-7xl px-2 sm:px-4">
    {{-- Mensaje de confirmación --}}
    @if (session('mensaje'))
        <section class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
            {{ session('mensaje') }}
        </section>
    @endif

    @if (! $plantillaSeleccionada)
        {{-- ========================================================= --}}
        {{-- LISTADO GENERAL DE PLANTILLAS                             --}}
        {{-- ========================================================= --}}

        {{-- Encabezado --}}
        <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-blue-950 to-indigo-900 px-6 py-4 text-white shadow-lg">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-blue-200">
                        Administración
                    </p>

                    <h1 class="mt-1.5 text-2xl font-black tracking-tight">
                        Plantillas de evaluación
                    </h1>

                    <p class="mt-1.5 max-w-3xl text-sm leading-5 text-blue-100">
                        Administre formularios completos de evaluación:
                        encabezado, dimensiones y preguntas.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="crearPlantilla"
                    class="w-fit rounded-lg bg-white px-4 py-2 text-xs font-black text-blue-900 shadow-sm transition hover:bg-blue-50"
                >
                    + Nueva plantilla
                </button>
            </div>
        </section>

        {{-- Contenedor principal --}}
        <section class="mt-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-700">
                        Formularios disponibles
                    </p>

                    <h2 class="mt-1 text-xl font-black tracking-tight text-slate-950">
                        Listado de plantillas
                    </h2>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Cree una plantilla nueva o administre su estructura completa.
                    </p>
                </div>

                {{-- Buscador --}}
                <div class="w-full sm:w-80">
                    <label
                        for="buscar"
                        class="text-[10px] font-black uppercase tracking-wide text-slate-500"
                    >
                        Buscar plantilla
                    </label>

                    <input
                        id="buscar"
                        type="text"
                        wire:model.live.debounce.300ms="buscar"
                        placeholder="Código, nombre o descripción..."
                        class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                    >
                </div>
            </div>

            {{-- Tarjetas de plantillas --}}
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                @forelse ($plantillas as $plantilla)
                    @php
                        $totalDimensiones = $plantilla->dimensiones
			        ->filter(fn ($dimension) => (int) $dimension->activo === 1)
			        ->count();

			    $totalPreguntas = $plantilla->dimensiones
			        ->filter(fn ($dimension) => (int) $dimension->activo === 1)
			        ->sum(
			            fn ($dimension) => $dimension->preguntas
			                ->filter(fn ($pregunta) => (int) $pregunta->activo === 1)
			                ->count()
                             );

                        $estiloPlantilla = match ($plantilla->codigo) {
                            'GER' => [
                                'fondo' => 'bg-blue-50',
                                'texto' => 'text-blue-700',
                                'borde' => 'border-blue-200',
                            ],
                            'MM' => [
                                'fondo' => 'bg-indigo-50',
                                'texto' => 'text-indigo-700',
                                'borde' => 'border-indigo-200',
                            ],
                            'OPE' => [
                                'fondo' => 'bg-cyan-50',
                                'texto' => 'text-cyan-700',
                                'borde' => 'border-cyan-200',
                            ],
                            default => [
                                'fondo' => 'bg-slate-50',
                                'texto' => 'text-slate-700',
                                'borde' => 'border-slate-200',
                            ],
                        };
                    @endphp

                    <article
                        wire:key="plantilla-{{ $plantilla->id }}"
                        class="rounded-2xl border {{ $estiloPlantilla['borde'] }} bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full {{ $estiloPlantilla['fondo'] }} px-3 py-1 text-[10px] font-black uppercase tracking-wide {{ $estiloPlantilla['texto'] }}">
                                        {{ $plantilla->codigo }}
                                    </span>

                                    @if ($plantilla->activo)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-700">
                                            Activa
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-slate-500">
                                            Inactiva
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-3 text-lg font-black text-slate-950">
                                    {{ $plantilla->nombre }}
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    {{ $plantilla->descripcion ?: 'Sin descripción registrada.' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-slate-50 px-3 py-2">
                                <p class="text-[10px] font-black uppercase tracking-wide text-slate-400">
                                    Dimensiones
                                </p>

                                <p class="mt-0.5 text-xl font-black text-slate-900">
                                    {{ $totalDimensiones }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 px-3 py-2">
                                <p class="text-[10px] font-black uppercase tracking-wide text-slate-400">
                                    Preguntas
                                </p>

                                <p class="mt-0.5 text-xl font-black text-slate-900">
                                    {{ $totalPreguntas }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-3">
                            <button
                                type="button"
                                wire:click="cambiarEstadoPlantilla({{ $plantilla->id }})"
                                wire:confirm="¿Está seguro de cambiar el estado de esta plantilla?"
                                class="
                                    rounded-lg px-3 py-1.5 text-[11px] font-black text-white transition
                                    {{ $plantilla->activo
                                        ? 'bg-red-600 hover:bg-red-700'
                                        : 'bg-emerald-600 hover:bg-emerald-700'
                                    }}
                                "
                            >
                                {{ $plantilla->activo ? 'Desactivar' : 'Activar' }}
                            </button>

                            <button
                                type="button"
                                wire:click="administrar({{ $plantilla->id }})"
                                class="rounded-lg bg-blue-700 px-3 py-1.5 text-[11px] font-black text-white transition hover:bg-blue-800"
                            >
                                Administrar
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 lg:col-span-2">
                        <h3 class="text-sm font-black text-slate-800">
                            No se encontraron plantillas
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Cambie el texto de búsqueda o registre una plantilla nueva.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold text-slate-600">
                    {{ $plantillas->count() }}
                    {{ $plantillas->count() === 1 ? 'plantilla' : 'plantillas' }}
                </span>
            </div>
        </section>
    @else
        {{-- ========================================================= --}}
        {{-- EDITOR INTEGRAL DE UNA PLANTILLA                          --}}
        {{-- ========================================================= --}}

        @php
	        $totalPreguntasPlantilla = $plantillaSeleccionada->dimensiones
	        ->filter(fn ($dimension) => (int) $dimension->activo === 1)
	        ->sum(
	            fn ($dimension) => $dimension->preguntas
	                ->filter(fn ($pregunta) => (int) $pregunta->activo === 1)
	                ->count()
	        );          
        @endphp

        {{-- Encabezado del editor --}}
        <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-blue-950 to-indigo-900 px-6 py-4 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-blue-200">
                        Editor integral de plantilla
                    </p>

                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-wide ring-1 ring-white/20">
                            {{ $plantillaSeleccionada->codigo }}
                        </span>

                        @if ($plantillaSeleccionada->activo)
                            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-100 ring-1 ring-emerald-300/20">
                                Activa
                            </span>
                        @else
                            <span class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-slate-200 ring-1 ring-white/20">
                                Inactiva
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-2 text-2xl font-black tracking-tight">
                        {{ $plantillaSeleccionada->nombre }}
                    </h1>

                    <p class="mt-1.5 max-w-3xl text-sm leading-5 text-blue-100">
                        {{ $plantillaSeleccionada->descripcion ?: 'Sin descripción registrada.' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        wire:click="editarPlantilla({{ $plantillaSeleccionada->id }})"
                        class="rounded-lg bg-white/10 px-4 py-2 text-xs font-black text-white ring-1 ring-white/20 transition hover:bg-white/20"
                    >
                        Editar encabezado
                    </button>

                    <button
                        type="button"
                        wire:click="volverAlListado"
                        class="rounded-lg bg-white px-4 py-2 text-xs font-black text-blue-900 transition hover:bg-blue-50"
                    >
                        Volver al listado
                    </button>
                </div>
            </div>
        </section>

        {{-- Resumen --}}
        <section class="mt-4 grid gap-3 sm:grid-cols-3">
            <article class="rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200">
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                    Código
                </p>

                <p class="mt-1 text-lg font-black text-slate-950">
                    {{ $plantillaSeleccionada->codigo }}
                </p>
            </article>

            <article class="rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200">
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                    Dimensiones
                </p>

                <p class="mt-1 text-lg font-black text-slate-950">
                    {{ $plantillaSeleccionada->dimensiones->count() }}
                </p>
            </article>

            <article class="rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200">
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                    Preguntas
                </p>

                <p class="mt-1 text-lg font-black text-slate-950">
                    {{ $totalPreguntasPlantilla }}
                </p>
            </article>
        </section>

        {{-- Dimensiones --}}
        <section class="mt-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-700">
                        Estructura del formulario
                    </p>

                    <h2 class="mt-1 text-xl font-black tracking-tight text-slate-950">
                        Dimensiones y preguntas
                    </h2>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Despliegue una dimensión para consultar o modificar sus preguntas.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="crearDimension"
                    class="w-fit rounded-lg bg-blue-700 px-4 py-2 text-xs font-black text-white shadow-sm transition hover:bg-blue-800"
                >
                    + Nueva dimensión
                </button>
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($plantillaSeleccionada->dimensiones as $dimension)
                    <article
                        wire:key="dimension-{{ $dimension->id }}"
                        class="rounded-xl border border-slate-200 bg-slate-50"
                    >
                        <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-blue-700">
                                        Orden {{ $dimension->orden }}
                                    </span>

                                    @if ($dimension->activo)
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-700">
                                            Activa
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-200 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-slate-500">
                                            Inactiva
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-2 text-base font-black text-slate-950">
                                    {{ $dimension->nombre }}
                                </h3>

                                @if ($dimension->factor)
                                    <p class="mt-1 text-xs text-slate-500">
                                        Factor: {{ $dimension->factor }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @php
                                    $totalPreguntasActivas = $dimension->preguntas
				        ->filter(fn ($pregunta) => (int) $pregunta->activo === 1)
				        ->count();
				@endphp


                                <span class="rounded-full bg-white px-3 py-1 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200">
                                    {{ $totalPreguntasActivas }}
                                    {{ $totalPreguntasActivas === 1 ? 'pregunta activa' : 'preguntas activas' }}
                                </span>

                                <button
                                    type="button"
                                    wire:click="editarDimension({{ $dimension->id }})"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-black text-slate-700 transition hover:bg-slate-100"
                                >
                                    Editar
                                </button>

                                <button
                                    type="button"
                                    wire:click="cambiarEstadoDimension({{ $dimension->id }})"
                                    wire:confirm="¿Está seguro de cambiar el estado de esta dimensión?"
                                    class="
                                        rounded-lg px-3 py-1.5 text-[11px] font-black text-white transition
                                        {{ $dimension->activo
                                            ? 'bg-red-600 hover:bg-red-700'
                                            : 'bg-emerald-600 hover:bg-emerald-700'
                                        }}
                                    "
                                >
                                    {{ $dimension->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </div>
                        </div>

                        {{-- Preguntas desplegables --}}
                        <details class="group">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-xs font-black text-blue-700 transition hover:bg-blue-50">
                                <span>
                                    Ver preguntas de esta dimensión
                                </span>

                                <span class="transition group-open:rotate-180">
                                    ▼
                                </span>
                            </summary>

                            <div class="border-t border-slate-200 bg-white p-4">
                                <div class="flex justify-end">
                                    <button
                                        type="button"
                                        wire:click="crearPregunta({{ $dimension->id }})"
                                        class="rounded-lg bg-indigo-700 px-3 py-1.5 text-[11px] font-black text-white transition hover:bg-indigo-800"
                                    >
                                        + Nueva pregunta
                                    </button>
                                </div>

                                <div class="mt-3 space-y-2">
                                    @forelse ($dimension->preguntas as $pregunta)
                                        <div
                                            wire:key="pregunta-{{ $pregunta->id }}"
                                            class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 sm:flex-row sm:items-center sm:justify-between"
                                        >
                                            <div class="flex gap-3">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[11px] font-black text-blue-700">
                                                    {{ $pregunta->orden }}
                                                </span>

                                                <div>
                                                    <p class="text-sm font-semibold leading-5 text-slate-700">
                                                        {{ $pregunta->texto }}
                                                    </p>

                                                    <p class="mt-1 text-[10px] font-black uppercase tracking-wide {{ $pregunta->activo ? 'text-emerald-700' : 'text-slate-400' }}">
                                                        {{ $pregunta->activo ? 'Pregunta activa' : 'Pregunta inactiva' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex shrink-0 flex-wrap gap-2">
                                                <button
                                                    type="button"
                                                    wire:click="editarPregunta({{ $pregunta->id }})"
                                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[11px] font-black text-slate-700 transition hover:bg-slate-100"
                                                >
                                                    Editar
                                                </button>

                                                <button
                                                    type="button"
                                                    wire:click="cambiarEstadoPregunta({{ $pregunta->id }})"
                                                    wire:confirm="¿Está seguro de cambiar el estado de esta pregunta?"
                                                    class="
                                                        rounded-lg px-3 py-1.5 text-[11px] font-black text-white transition
                                                        {{ $pregunta->activo
                                                            ? 'bg-red-600 hover:bg-red-700'
                                                            : 'bg-emerald-600 hover:bg-emerald-700'
                                                        }}
                                                    "
                                                >
                                                    {{ $pregunta->activo ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                                            <p class="text-xs leading-5 text-slate-500">
                                                Esta dimensión todavía no tiene preguntas registradas.
                                            </p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
                        <h3 class="text-sm font-black text-slate-800">
                            Esta plantilla todavía no tiene dimensiones
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Presione “Nueva dimensión” para comenzar a construir el formulario.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    @endif

    {{-- ========================================================= --}}
    {{-- MODAL DE PLANTILLA                                        --}}
    {{-- ========================================================= --}}
    @if ($mostrarModalPlantilla)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
            wire:click.self="cerrarModalPlantilla"
        >
            <section class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="bg-gradient-to-r from-slate-950 via-blue-950 to-indigo-900 px-5 py-4 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-200">
                                Encabezado del formulario
                            </p>

                            <h2 class="mt-1 text-xl font-black">
                                {{ $plantillaId ? 'Editar plantilla' : 'Nueva plantilla' }}
                            </h2>
                        </div>

                        <button
                            type="button"
                            wire:click="cerrarModalPlantilla"
                            class="rounded-lg bg-white/10 px-3 py-1.5 text-sm font-black text-white transition hover:bg-white/20"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <form wire:submit="guardarPlantilla" class="space-y-4 p-5">
                    <div>
                        <label for="codigoPlantilla" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Código
                        </label>

                        <input
                            id="codigoPlantilla"
                            type="text"
                            wire:model="codigoPlantilla"
                            maxlength="20"
                            placeholder="Ej. SUP"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm uppercase outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                        >

                        @error('codigoPlantilla')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombrePlantilla" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Nombre
                        </label>

                        <input
                            id="nombrePlantilla"
                            type="text"
                            wire:model="nombrePlantilla"
                            maxlength="150"
                            placeholder="Ej. Plantilla para supervisores"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                        >

                        @error('nombrePlantilla')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="descripcionPlantilla" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Descripción
                        </label>

                        <textarea
                            id="descripcionPlantilla"
                            wire:model="descripcionPlantilla"
                            rows="3"
                            maxlength="1000"
                            placeholder="Describa el objetivo de esta plantilla..."
                            class="mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                        ></textarea>

                        @error('descripcionPlantilla')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <input
                            type="checkbox"
                            wire:model="plantillaActiva"
                            class="h-4 w-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600"
                        >

                        <span>
                            <span class="block text-sm font-black text-slate-800">
                                Plantilla activa
                            </span>

                            <span class="block text-xs text-slate-500">
                                Las plantillas activas pueden asignarse a puestos.
                            </span>
                        </span>
                    </label>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            wire:click="cerrarModalPlantilla"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-50"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-black text-white transition hover:bg-blue-800"
                        >
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MODAL DE DIMENSIÓN                                        --}}
    {{-- ========================================================= --}}
    @if ($mostrarModalDimension)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
            wire:click.self="cerrarModalDimension"
        >
            <section class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-blue-900 px-5 py-4 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200">
                                Estructura de la plantilla
                            </p>

                            <h2 class="mt-1 text-xl font-black">
                                {{ $dimensionId ? 'Editar dimensión' : 'Nueva dimensión' }}
                            </h2>
                        </div>

                        <button
                            type="button"
                            wire:click="cerrarModalDimension"
                            class="rounded-lg bg-white/10 px-3 py-1.5 text-sm font-black text-white transition hover:bg-white/20"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <form wire:submit="guardarDimension" class="space-y-4 p-5">
                    <div>
                        <label for="nombreDimension" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Nombre de la dimensión
                        </label>

                        <input
                            id="nombreDimension"
                            type="text"
                            wire:model="nombreDimension"
                            maxlength="200"
                            placeholder="Ej. Organización del trabajo"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('nombreDimension')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="factorDimension" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Factor
                        </label>

                        <input
                            id="factorDimension"
                            type="text"
                            wire:model="factorDimension"
                            maxlength="255"
                            placeholder="Ej. Comunicación efectiva"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('factorDimension')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ordenDimension" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Orden
                        </label>

                        <input
                            id="ordenDimension"
                            type="number"
                            wire:model="ordenDimension"
                            min="1"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('ordenDimension')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <input
                            type="checkbox"
                            wire:model="dimensionActiva"
                            class="h-4 w-4 rounded border-slate-300 text-indigo-700 focus:ring-indigo-600"
                        >

                        <span>
                            <span class="block text-sm font-black text-slate-800">
                                Dimensión activa
                            </span>

                            <span class="block text-xs text-slate-500">
                                Las dimensiones activas se incluyen en el formulario.
                            </span>
                        </span>
                    </label>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            wire:click="cerrarModalDimension"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-50"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-indigo-700 px-4 py-2 text-xs font-black text-white transition hover:bg-indigo-800"
                        >
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- MODAL DE PREGUNTA                                         --}}
    {{-- ========================================================= --}}
    @if ($mostrarModalPregunta)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
            wire:click.self="cerrarModalPregunta"
        >
            <section class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="bg-gradient-to-r from-slate-950 via-cyan-950 to-teal-800 px-5 py-4 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-200">
                                Criterio de evaluación
                            </p>

                            <h2 class="mt-1 text-xl font-black">
                                {{ $preguntaId ? 'Editar pregunta' : 'Nueva pregunta' }}
                            </h2>
                        </div>

                        <button
                            type="button"
                            wire:click="cerrarModalPregunta"
                            class="rounded-lg bg-white/10 px-3 py-1.5 text-sm font-black text-white transition hover:bg-white/20"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <form wire:submit="guardarPregunta" class="space-y-4 p-5">
                    <div>
                        <label for="dimensionPreguntaId" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Dimensión
                        </label>

                        <select
                            id="dimensionPreguntaId"
                            wire:model="dimensionPreguntaId"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                        >
                            <option value="">
                                Seleccione una dimensión
                            </option>

                            @foreach ($plantillaSeleccionada?->dimensiones ?? [] as $dimension)
                                <option value="{{ $dimension->id }}">
                                    Orden {{ $dimension->orden }}
                                    ·
                                    {{ $dimension->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('dimensionPreguntaId')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="textoPregunta" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Texto de la pregunta
                        </label>

                        <textarea
                            id="textoPregunta"
                            wire:model="textoPregunta"
                            rows="4"
                            maxlength="1000"
                            placeholder="Escriba el criterio que será evaluado..."
                            class="mt-1.5 w-full resize-none rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                        ></textarea>

                        @error('textoPregunta')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ordenPregunta" class="text-xs font-black uppercase tracking-wide text-slate-600">
                            Orden
                        </label>

                        <input
                            id="ordenPregunta"
                            type="number"
                            wire:model="ordenPregunta"
                            min="1"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                        >

                        @error('ordenPregunta')
                            <p class="mt-1.5 text-xs font-bold text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <input
                            type="checkbox"
                            wire:model="preguntaActiva"
                            class="h-4 w-4 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600"
                        >

                        <span>
                            <span class="block text-sm font-black text-slate-800">
                                Pregunta activa
                            </span>

                            <span class="block text-xs text-slate-500">
                                Las preguntas activas se incluyen en el formulario.
                            </span>
                        </span>
                    </label>

                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            wire:click="cerrarModalPregunta"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-50"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-cyan-700 px-4 py-2 text-xs font-black text-white transition hover:bg-cyan-800"
                        >
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif
</div>
