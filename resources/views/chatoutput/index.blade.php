<!-- resources/views/tweet/show.blade.php -->

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('show contents') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:w-8/12 md:w-1/2 lg:w-5/12">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <div class="mb-6">
            <div class="relative overflow-x-auto">
              <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-white border-b border-gray-200 dark:text-gray-400">
                  <tr>
                    <th scope="col" class="px-6 py-3">text</th>
                    <th scope="col" class="px-6 py-3">score</th>
                    <th scope="col" class="px-6 py-3"> bad points</th>
                    <th scope="col" class="px-6 py-3">categorised</th>
                  </tr>
                </thead>
                <tbody>
                  @for ($count=0;$count<$count_data;$count++)
                  <tr class="bg-white border-b border-gray-200">
                    <td class="px-6 py-4">{{$user_inputs[$count]->sentence}}</td>
                    @if(isset($user_outputs[$count]))
                    <td class="px-6 py-4">{{$user_outputs[$count]->score}}</td>
                    @endif
                    <td class="px-6 py-4">
                      <ul>
                        @if($user_outputs[$count]->kanji_rate>=0.2)
                        <li>漢字が多いです!</li>
                        @endif
                        @if($user_outputs[$count]->emoji_rate<=0.2)
                        <li>絵文字が少ないです!</li>
                        @endif
                        @foreach($user_outputs_keigo_lis[$count] as $user_output_keigo)
                        <li>"{{$user_output_keigo->keigo}}"があります。</li>
                        @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4">
                      @if($user_outputs[$count]->naive_bayes==0)
                      部下から上司に対する文章っぽいです！
                      @endif
                      @if($user_outputs[$count]->naive_bayes==1)
                      上司から部下に対する文章っぽいです！
                      @endif
                      @if($user_outputs[$count]->naive_bayes==2)
                      同僚同士の文章っぽいです！
                      @endif
                      @if($user_outputs[$count]->naive_bayes==3)
                      友達同士の文章っぽいです！
                      @endif
                      @if($user_outputs[$count]->naive_bayes==4)
                      カップルのラブラブチャットっぽいです！
                      @endif
                    </td>
                  </tr>
                  @endfor
                </tbody>
              </table>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>