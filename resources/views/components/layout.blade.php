<x-base-layout>

    @isset($title)
        <x-slot:title>{{$title}}</x-slot:title>
    @endisset

    <div class="absolute w-full h-full top-0 left-0 right-0 bottom-0 overflow-auto flex flex-row justify-start items-start gap-0">
        <x-main-menu />

        <div class="w-full bg-gray-50 h-full">
            {{$slot}}
        </div>
    </div>


</x-base-layout>