<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $productoId ? 'Editar' : 'Nuevo' }} Producto Personal
        </h2>
    @endsection

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('productos.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Volver
                        </a>
                    </div>

                    <form wire:submit="guardar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Producto</label>
                                <input wire:model="nombre" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Categoría -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                                <select wire:model="categoria_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('categoria_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600 italic">Valores nutricionales por 100g de producto</p>
                            </div>

                            <!-- Calorías -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Calorías (kcal)</label>
                                <input wire:model="calorias" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('calorias') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Proteínas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Proteínas (g)</label>
                                <input wire:model="proteinas" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('proteinas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Carbohidratos -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Carbohidratos (g)</label>
                                <input wire:model="carbohidratos" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('carbohidratos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Fibra -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fibra (g)</label>
                                <input wire:model="fibra" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('fibra') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Grasa Total -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Grasa Total (g)</label>
                                <input wire:model="grasa_total" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('grasa_total') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Grasa Saturada -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Grasa Saturada (g)</label>
                                <input wire:model="grasa_saturada" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('grasa_saturada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Grasa Monoinsaturada -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Grasa Monoinsaturada (g)</label>
                                <input wire:model="grasa_monoinsaturada" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('grasa_monoinsaturada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Grasa Poliinsaturada -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Grasa Poliinsaturada (g)</label>
                                <input wire:model="grasa_poliinsaturada" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('grasa_poliinsaturada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Grasa Trans -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Grasa Trans (g)</label>
                                <input wire:model="grasa_trans" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('grasa_trans') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Colesterol -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Colesterol (mg)</label>
                                <input wire:model="colesterol" type="number" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('colesterol') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('productos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ $productoId ? 'Actualizar' : 'Guardar' }} Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
