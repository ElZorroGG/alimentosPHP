<div>
    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mis Objetivos Nutricionales
        </h2>
    @endsection

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Define tus objetivos nutricionales diarios. El sistema te mostrará el porcentaje consumido y avisos si superas o no alcanzas tus metas.
                        </p>
                    </div>

                    <form wire:submit="guardar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Calorías Diarias (kcal)
                                </label>
                                <input 
                                    wire:model="objetivo_calorias" 
                                    type="number" 
                                    step="0.01"
                                    placeholder="Ej: 2000"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('objetivo_calorias') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Proteínas Diarias (g)
                                </label>
                                <input 
                                    wire:model="objetivo_proteinas" 
                                    type="number" 
                                    step="0.01"
                                    placeholder="Ej: 150"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('objetivo_proteinas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Carbohidratos Diarios (g)
                                </label>
                                <input 
                                    wire:model="objetivo_carbohidratos" 
                                    type="number" 
                                    step="0.01"
                                    placeholder="Ej: 250"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('objetivo_carbohidratos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Grasas Diarias (g)
                                </label>
                                <input 
                                    wire:model="objetivo_grasas" 
                                    type="number" 
                                    step="0.01"
                                    placeholder="Ej: 65"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('objetivo_grasas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Guardar Objetivos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
