<x-layouts.app :title="__('Users')">
   <h1>Users {{$count}} </h1>
    <h2>
        Page: {{$users->currentPage()}} of {{$users->lastPage()}}
    </h2>
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }} - {{ $user->email }}</li>
        @endforeach
    </ul>

    {{$users->links()}}
</x-layouts.app>
