<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Chat Output') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:w-10/12 md:w-8/10 lg:w-8/12">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <table class="text-center w-full border-collapse">
            <thead>
              <tr>
                <th class="py-4 px-6 bg-grey-lightest font-bold uppercase text-lg text-grey-dark border-b border-grey-light">{{$group->name}}</th>
              </tr>
            </thead>
            <tbody>
              <tr class="hover:bg-grey-lighter">
                <td class="py-4 px-6 border-b border-grey-light">
                  <a href="{{route('chatoutput.show',Auth::user()->id)}}">
                    <h3 class="text-left font-bold text-lg text-grey-dark">{{Auth::user()->name}}</h3>
                  </a>
                   <div class="flex justify-end inline-block">
                    a
                  </div>
                </td>
              </tr>
              @foreach ($group_users as $group_user)
              @if ($group_user->id==Auth::user()->id)
                @continue
              @endif
              <tr class="hover:bg-grey-lighter">
                <td class="py-4 px-6 border-b border-grey-light">
                  <a href="{{route('chatoutput.show',$group_user->id)}}">
                  <h3 class="text-left font-bold text-lg text-grey-dark">{{$group_user->name}}</h3>
                  </a>
                  <div class="flex justify-end inline-block">
                    a
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>