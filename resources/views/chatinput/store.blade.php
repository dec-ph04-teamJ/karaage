<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('あなたがうった文章') }}
    </h2>
  </x-slot>

{{$result->sentence}}
</x-app-layout>