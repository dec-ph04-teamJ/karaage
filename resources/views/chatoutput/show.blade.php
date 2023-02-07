<!-- resources/views/tweet/show.blade.php -->

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('show result') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:w-8/12 md:w-1/2 lg:w-5/12">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <div class="mb-6">
            <div class="flex flex-col mb-4">
              <p class="mb-2 uppercase font-bold text-lg text-grey-darkest">Contents</p>
              <p class="py-2 px-3 text-grey-darkest" id="sentence">
                {{$result_input->sentence}}
              </p>
              <p class="py-2 px-3 text-grey-darkest" id="score">
                {{$result_output->score}}
              </p>
              @foreach($keigo_lis as $keigo)
              <p class="py-2 px-3 text-grey-darkest" id="score">
                "{{$keigo}}"があります。修正しましょう!
              </p>
              @endforeach
              @if($result_output->kanji_rate>=0.2)
              <p class="py-2 px-3 text-grey-darkest" id="score">
                漢字が多いです!もっと柔らかくしましょう!
              </p>
              @endif
              @if($result_output->emoji_rate<=0.2)
              <p class="py-2 px-3 text-grey-darkest" id="score">
                絵文字が少ないです!もっと使いましょう!
              </p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>