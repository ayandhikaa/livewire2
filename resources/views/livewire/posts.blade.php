<x-slot name="header">
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">

            @if (session()->has('message'))
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                    <div class="flex">
                        <div>
                            <p class="text-sm">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Container untuk Create, Paginate, dan Search -->
            <div class="flex justify-between items-center mb-4">
                <!-- Create Button -->
                <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create
                </button>

                <!-- Dropdown Paginate dan Search -->
                <div class="flex space-x-4">
                    <!-- Dropdown Paginate -->
                    <select wire:model.live="rowperPage" class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-200 focus:border-blue-200 px-6">
                        <option value="2">2</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="20">20</option>
                    </select>

                    <!-- Search Input -->
                    <input type="text" wire:model.live="search" placeholder="Cari..." class="border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                </div>
            </div>

            @if($isOpen)
                @include('livewire.create')
            @endif

            <!-- Tabel Data -->
            <table class="table-fixed w-full border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 w-20">No.</th>
                        <th class="px-4 py-2">Foto</th>
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($posts as $index => $post)
                        <tr>
                            <td class="border px-4 py-2 text-center">{{ ($posts->currentPage() - 1) * $posts->perPage() + $index + 1 }}</td>
                            <td class="border px-4 py-2 text-center">
                                @if($post->photo)
                                <img src="{{ Storage::url($post->photo) }}" alt="Post Photo">
                                @else
                                    <span>No Photo</span>
                                @endif
                            </td>
                            <td class="border px-4 py-2">{{ $post->title }}</td>
                            <td class="border px-4 py-2">{{ $post->description }}</td>
                            <td class="border px-4 py-2 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button wire:click="edit({{ $post->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $post->id }})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <!-- Pagination Links -->
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
