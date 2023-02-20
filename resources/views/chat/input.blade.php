<x-app-layout>
    <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('chat room') }}
    </h2>
    </x-slot>
    

    <div class="flex flex-col mb-4">
        <div class="p-6 bg-white border-b border-gray-200">
        @foreach ($inputs as $input)
            <div> {{$input->sentence}}</div>
        @endforeach
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:w-8/12 md:w-1/2 lg:w-5/12">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form class="mb-6" action="{{ route('chat.store') }}" method="POST">
                @csrf
                <div class="flex flex-col mb-4">
                    <label class="mb-2 uppercase font-bold text-lg text-grey-darkest" for="sentence">chat入力欄</label>
                    <input class="border py-2 px-3 text-grey-darkest" type="text" name="sentence" id="sentence">
                </div>
    
                <div class="flex flex-row  mb-4">
                        <button class="text-center w-full py-3 mt-6 font-medium tracking-widest text-black uppercase bg-white shadow-lg focus:outline-none hover:bg-white hover:text-black hover:shadow-none">
                        <a href="{{route('chatoutput.index')}}">
                        今までのチャットスコア
                        </a>
                        </button>
                    <button type="submit" class="text-center w-full py-3 mt-6 font-medium tracking-widest text-white uppercase bg-black shadow-lg focus:outline-none hover:bg-gray-900 hover:shadow-none">
                        送信
                    </button>
                    </form>
                </div>
            </div>
            </div>
        </div>
</x-app-layout>


