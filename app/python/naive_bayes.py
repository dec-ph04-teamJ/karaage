import MeCab
from keras.preprocessing.text import Tokenizer
import itertools
import numpy as np
import pandas as pd
import sys

# MeCabのオブジェクト生成
wakati = MeCab.Tagger("-Owakati")
# chasen = MeCab.Tagger("-Ochasen") # なんかエラー出た
tagger = MeCab.Tagger()

# -------------------------------------
class NaiveBayes:
  # ここobject指向っぽい
  def __init__(self):
    self
  # def __init__(self, tokenizer):
  #   self.tokenizer = tokenizer
  

  # categoryクラスの中の文章を分かち書き
  def wakatigaki_all(self, categories):
    wakati = MeCab.Tagger("-Owakati")
    categories_1d = list(itertools.chain.from_iterable(categories))
    wakati_categories = []
    for i in range(len(categories_1d)):
      wakati_categories.append(wakati.parse(categories_1d[i]))
    # print(wakati_categories)
    return wakati_categories

  # 各カテゴリの文章を分かち書き
  def wakatigaki_each(self, category):
    wakati = MeCab.Tagger("-Owakati")
    wakati_category = []
    for i in range(len(category)):
      wakati_category.append(wakati.parse(category[i]))
    # print(wakati_category)
    return wakati_category

  # 分かち書きされた文章をbinary化
  def binary_vector(self, categories):
    wakati_categories = self.wakatigaki_all(categories)
    wakati_category = []
    for i in categories:
      wakati_category.append(self.wakatigaki_each(i))
    # print(wakati_category)
    # print(wakati_categories)
    tokenizer = Tokenizer()
    tokenizer.fit_on_texts(wakati_categories)
    # print("すべての文章の数：", tokenizer.document_count)
    N_all = tokenizer.document_count
    # print("単語ごとのインデックス：", tokenizer.word_index)
    # binary_categories = tokenizer.texts_to_matrix(wakati_categories, "binary")
    binary_category = []
    for i in wakati_category:
      binary_category.append(tokenizer.texts_to_matrix(i, "binary"))
    return binary_category

  # 各単語の統計値
  def statistics_word(self, categories, C):
    binary_category = self.binary_vector(categories)
    N_words = np.zeros((C,len(binary_category[0][0])))
    # print(binary_category[0][0])
    for i in range(len(binary_category)):
      for j in range(len(binary_category[0])):
        # print(type(binary_category[i][j]))
        N_words[i,:] = N_words[i,:] + binary_category[i][j]
    # print(N_words)
    return N_words

  # MAP推定
  def MAP_estimation(self, categories, alpha, C):
    N_words = self.statistics_word(categories, C)
    # なんかtokenaizerが変わってたのでもう一回する
    tokenizer = Tokenizer()
    tokenizer.fit_on_texts(self.wakatigaki_all(categories))
    N_sentences_all = tokenizer.document_count
    N_sentences = np.zeros((len(categories),1))
    for i in range(len(categories)):
      N_sentences[i,0] = len(categories[i])
    # categoryの確率
    p_category = np.zeros((len(categories),1))
    for i in range(len(categories)):
      p_category[i,0] = (N_sentences[i,0] + (alpha-1)) / (N_sentences_all + C * (alpha-1))
    # print(N_sentences)
    # print(p_category)
    # 各categoryにおける各単語の確率
    p_words = np.zeros((len(categories), len(N_words[0])))
    for i in range(len(categories)):
      for j in range(len(N_words[0])):
        p_words[i,j] = (N_words[i,j] + (alpha-1)) / (N_sentences[i,0] + C * (alpha-1))
    # print(p_category)
    # print(p_words)
    return p_category, p_words

  # テストデータを入れて確認
  def test(self, categories, categories_name, alpha, C, test_data):
    p_category = self.MAP_estimation(categories, alpha, C)[0]
    p_words = self.MAP_estimation(categories, alpha, C)[1]

    wakati_test = wakati.parse(test_data)
    tokenizer = Tokenizer() # またtokenizer作り
    tokenizer.fit_on_texts(self.wakatigaki_all(categories))
    # testdataをbinary化
    binary_test = tokenizer.texts_to_matrix([wakati_test], "binary")
    # どのcategoryになる確率が一番高いかを計算
    p_test = np.zeros((len(p_category),1))
    for i in range(len(p_category)):
      p_test[i,0] = p_category[i,0]
      for j in range(len(binary_test[0])):
        if binary_test[0][j] == 1.0:
          p_test[i,0] = p_test[i,0] * p_words[i][j]
        elif binary_test[0][j] == 0.0:
          p_test[i,0] = p_test[i,0] * (1 - p_words[i][j])
    print("尤度最大のカテゴリ：", categories_name[np.argmax(p_test)])
    return p_test
# -------------------------------------
# 学習データ
# chatGPT で作った

def train_data_1():
  to_boss = ["おはようございます．", "お疲れ様です．", "夜分遅くに失礼致します．", "お忙しいところ恐れ入ります．", 
            "こんにちは、はい、今はちょうど手が離せます。", 
            "何かありましたらおっしゃってください。", 
            "はい、そうなんです。大変ありがたいです", 
            "〇〇の不具合があったため、作業が遅れてしまいました。", 
            "ただ、現在は修正が完了しましたので、次回の報告では数字が改善されると思います。",
            "はい、承知いたしました。", 
            "ありがとうございます。", 
            "こんにちは、はい、承知しました。", 
            "現在の進捗ですが、先週末までにA案件の完成度は80％、B案件は70％になりました。", 
            "A案件については、現在テスト中ですが、ほとんどの部分で問題がないと思われます。", 
            "今週中には最終的な品質チェックを完了させたいと思います。", 
            "はい、リソースについては確保されています。ただ、あと2週間ほどで完成させるには、まだいくつかの問題が残っています。現在、優先順位をつけて解決策を模索しているところです。", 
            "承知いたしました。", 
            "ありがとうございます。", 
            "はい、よろしくお願いします。", 
            "今日はどんな予定がありますか？", 
            "はい、昨日は新しいデザインを完成させて、プレゼン用の資料も作成しました。", 
            "今は最終チェックをしています。", 
            "はい、先週の会議の内容をまとめてメールで送信し、返信も受け取っています。", 
            "今週中に仕上げる予定です。", 
            "明日の朝には上司に確認していただけますか？", 
            "ありがとうございます。", 
            "引き続き努力します。", 
            "おはようございます．", "お疲れ様です．", "夜分遅くに失礼致します．", "お忙しいところ恐れ入ります．", 
            "大変ありがたいです．", 
            "本当に助かります．", 
            "かしこまりました．承知いたしました．", 
            "すごくいいですね．", 
            "よろしくお願い致します．"
            ]

  from_boss = ["おはよう．", "お疲れ．", "夜遅くに申し訳ない．", "忙しい時に申し訳ない．", 
              "こんにちは、〇〇さん。", 
              "今日はお疲れ様です。", 
              "いくつか確認したいことがあるのですが、今忙しいですか？", 
              "ありがとうございます。", 
              "昨日の報告書を拝見しましたが、いくつか質問があります。", 
              "〇〇の項目で、数字が前回と比べて下がっているようですが、何か原因がありましたか？", 
              "なるほど、ありがとうございます。", 
              "次回の報告まで、改善されるようチェックをしておきます。", 
              "他にも何か質問がある場合は、連絡してください。", 
              "今日の進捗について、報告をお願いできますか？", 
              "予定通り進んでいるようですね。", 
              "頑張って．", 
              "ただ、A案件の80％のうち、どの程度が本番に耐えられる品質でしょうか？", 
              "なるほど、了解しました。", 
              "B案件については、完成までに必要なリソースは十分に確保されていますか？", 
              "引き続き、努力をお願いいたします。", 
              "何か困難があった場合は、私に連絡してください。", 
              "こんにちは、今日もよろしく頼むよ。", 
              "まずは、昨日のプロジェクトの進捗状況を報告してもらえるかな？", 
              "いいね、それは順調そうだ。あと、来週の打ち合わせについてだけど、部署長との連絡はとれたか？", 
              "それは良かった。あとは、今週末に締め切りのある報告書についてはどうかな？",
              "了解した。それにしても、部下の仕事ぶりはいつも素晴らしいと思う。", 
              "今後もこの調子で頑張ってくれ。", 
              "おはよう．", "お疲れ．", "夜遅くに申し訳ない．", "忙しい時に申し訳ない．", 
              "ありがとう．", 
              "助かる．", 
              "わかった．了解．", 
              "とてもいいね", 
              "よろしく"
              ]

  colleague = ["おはようございます！", "お疲れ様です！", "夜遅くにすみません！", "忙しい時にすみません！", 
              "こんにちは、〇〇さん。昨日のプレゼンはどうだった？", 
              "こんにちは、まあまあだったかな。", 
              "質問に答えるのにちょっと手こずってしまって、スムーズに進まなかった感じ。", 
              "そうだったんですね。", 
              "でも、発表資料はわかりやすくまとめられていたと思います。", 
              "それに、提案内容も良かったと思いますよ。", 
              "ありがとうございます．ただ、もう少し練習しておけばよかったなという気がして、反省しています。", 
              "いいえ、そんなことないですよ。", 
              "ありがとうございます今後も精進していきたいと思います。", 
              "おはよう！〇〇さん。昨日の取引先との打ち合わせ、どうだった？", 
              "おはよう！まあまあだったよ。細かい部分で折り合いがつかなかったけど、全体的には問題なく進められたと思う。", 
              "そうか、よかったね。次に向けて、今後の対策は考えられた？", 
              "うん、ちょっと調べてみたんだけど、今回の問題点はこういう原因があったみたいだ。", 
              "なるほど、その対策案は良さそうだね。", 
              "うん、そうだね。引き続き、検討していきたいと思う。", 
              "わかった、了解。次回もよろしくね。", 
              "お疲れさま今日は忙しかったね。", 
              "お疲れさま、本当に忙しかったよ。でも、なんとか仕事を片付けることができたから、良かったと思う。", 
              "そうだね、頑張ったね。でも、そんなに忙しいとストレスがたまるから、今週末はゆっくり休んでね。", 
              "ありがとう、そうするよ。でも、〇〇さんも忙しかったでしょう？大丈夫？", 
              "うん、大丈夫だよ。でも、やっぱり疲れたなと感じるところもあるから、休日は家でのんびり過ごす予定だよ。", 
              "そうか、じゃあ、私も同じく家でのんびり過ごすよ。", 
              "そうだね、また来週からよろしくね。", 
              "おはようございます！", "お疲れ様です！", "夜遅くにすみません！", "忙しい時にすみません！", 
              "ありがとう！", 
              "ほんとたすかる！",
              "了解です！わかりました！" , 
              "最高ですね！", 
              "よろしく！"
              ]

  friend = ["おはよう！", "お疲れ！", "夜遅くにごめん！", "忙しい時にごめん！", 
            "最近どうしてた？", 
            "ああ、まあ、忙しかったよ。仕事もプライベートも。", 
            "そうなんだ。どんな仕事してるの？", 
            "ITの仕事をしてるんだ。最近は特に忙しくて、残業続きだったよ。", 
            "それは大変だね。私は最近、新しい趣味を始めたんだ。料理なんだ。自分で作った料理を食べるのが楽しくてね。", 
            "それはいいね。どんな料理を作ってるの？",
            "最近は、パスタや餃子なんかを作ってるよ。美味しくできると嬉しいんだ。", 
            "それはすごいね。今度、君の作った料理を食べさせてもらおうか？", 
            "いいよ。一緒に料理して食べようよ。", 
            "それは楽しそうだね。また連絡して、計画しようよ。",
            "おい、最近何してたんだよ？", 
            "ああ、色々あったよ。",
            "それはすごいね。次のライブの予定はあるの？まだ決まってないんだけど、近いうちにやる予定なんだ", 
            "そうなんだ。俺も見に行くから、誘ってくれよ。", 
            "いいよ、助かる。それにしても、最近は忙しくて友達との時間が取れなかったな。そうか、それなら今度、一緒に飲みに行こうよ。", 
            "いいね、それは楽しそうだ。次の休みにでも、計画しようよ。", 
            "ああ、俺は相変わらずだよ。仕事に追われてる毎日だ。", 
            "へえ、それはいいね。どんな人なの？",
            "すごくいい人だよ。同じ趣味があったり、性格も合う感じがする。", 
            "それはいいな。次は彼女を紹介してくれよ。", 
            "いいよ、それなら一緒に飲みに行こうか。" 
            "最近、自分で作った料理をSNSにアップするのが流行ってるじゃん。", 
            "そんなに自信あるかよ。まあ、次の飲み会で競い合おうじゃないか。", 
            "いいね。それにしても、久しぶりに会えて嬉しいな。俺もだよ。また近いうちに飲みに行こうな。", 
            "おはよう！", "お疲れ！", "夜遅くにごめん！", "忙しい時にごめん！", 
            "まじでありがとう！",
            "まじ助かる！" , 
            "りょーかい！わかった！" , 
            "めっちゃいいやん", 
            "よろしく！"
            ]

  couple = ["おはよー", "おつかれちゃん", "夜になったね♡", "いま忙しい？？", 
            "今日はありがとう、楽しかったわ。", 
            "いや、こちらこそ、楽しかったよ。一緒に遊びに行こうね。大好き", 
            "そうね、次はどこに行きたい？", 
            "いろいろ考えてるけど、どうかな？のんびり過ごすのも良さそうじゃない？", 
            "それもいいね。でも、今度は私が何か用意してあげるから、何が食べたい？",
            "じゃあ、チーズフォンデュとかいいんじゃない？", 
            "うん、分かった。次は私が用意するから、いいもの作ってあげるね。", 
            "今日は本当に楽しかったね。大好き♡", 
            "そうだね、君がいると俺も特別な気分になるよ。俺たちは、本当に幸せだと思う。", 
            "うん、そう思うわ。あなたと出会えたことが、本当に良かったと思う。", 
            "俺もそう思うよ。君と出会えて、本当に幸せなんだ。大好き♡",
            "私もそう思うわ。あなたと一緒にいると、本当に幸せな気持ちになれるから。愛してるよ。", 
            "俺も君を愛してるよ。これからも、ずっと愛し続けるから。", 
            "何でいつも遅れるの？もう約束の時間を守ってよ。", 
            "ごめん、ちょっと忙しかったんだ。遅れたことは謝るよ。", 
            "いつも遅れるから、もうウンザリ。私の時間を尊重してよ。",
            "分かってるよ。もう少し早く出るようにするから、許してくれ。", 
            "それだけじゃないわ。最近は全然私とデートしないし、何か変なの？", 
            "いや、別に変わったことはないよ。忙しいだけだから、デートする時間がなかったんだ。", 
            "そうなの？それなら、デートの日程をあらかじめ決めておいてよ。私も忙しいんだから、計画的にやらないと。", 
            "うん、分かった。もっとデートを計画的にやっていこうね。",
            "そうね。でも、今度は本当に時間を守ってね。", 
            "分かったよ。もう遅れないようにするから、許してくれ。", 
            "おはよー", "おつかれちゃん", "夜になったね♡", "いま忙しい？？", 
            "ありがと♡",
            "助かった♡",  
            "りょーかい♡ おっけー", 
            "大好き♡", 
            "これやって♡"
            ]
  all = [to_boss, from_boss, colleague, friend, couple]
  all_len = [len(to_boss), len(from_boss), len(colleague), len(friend), len(couple)]
  print(all_len)
  all_name = ["部下から上司", "上司から部下", "同僚", "友達", "カップル"]
  return all, all_name


# professor = ["大変ありがたいです", "本当にありがとうございます"]
# senpai = ["まじでありがとうございます！", "ほんとにありがとうございます！"]
# friend = ["がちありがとう！", "まじありがとう！"]
# all = [professor, senpai, friend]
def train_data_2():
  to_boss = ["おはようございます．", "お疲れ様です．", "夜分遅くに失礼致します．", "お忙しいところ恐れ入ります．", 
            "大変ありがたいです．", 
            "本当に助かります．", 
            "かしこまりました．承知いたしました．", 
            "すごくいいですね．", 
            "よろしくお願い致します．"
            ]

  from_boss = ["おはよう．", "お疲れ．", "夜遅くに申し訳ない．", "忙しい時に申し訳ない．", 
              "ありがとう．", 
              "助かる．", 
              "わかった．了解．", 
              "とてもいいね", 
              "よろしく"
              ]

  colleague = ["おはようございます！", "お疲れ様です！", "夜遅くにすみません！", "忙しい時にすみません！", 
              "ありがとう！", 
              "ほんとたすかる！",
              "了解です！わかりました！" , 
              "最高ですね！", 
              "よろしく！"
              ]

  friend = ["おはよう！", "お疲れ！", "夜遅くにごめん！", "忙しい時にごめん！", 
            "まじでありがとう！",
            "まじ助かる！" , 
            "りょーかい！わかった！" , 
            "めっちゃいいやん", 
            "よろしく！"
            ]

  couple = ["おはよー", "おつかれちゃん", "夜になったね♡", "いま忙しい？？", 
            "ありがと♡",
            "助かった♡",  
            "りょーかい♡ おっけー", 
            "大好き♡", 
            "これやって♡"
            ]
  all = [to_boss, from_boss, colleague, friend, couple]
  all_len = [len(to_boss), len(from_boss), len(colleague), len(friend), len(couple)]
  print(all_len)
  all_name = ["部下から上司", "上司から部下", "同僚", "友達", "カップル"]
  return all, all_name

# ---------------------------
# 使う学習データ
all, all_name = train_data_1()

# --------------------------------
# 実行したい
naivebayes = NaiveBayes()
p_category, p_words = naivebayes.MAP_estimation(all, 2, 5) # 学習器生成
# p_category = [[0.2], [0.2], [0.2], [0.2], [0.2]]
# p_words = [学習済みモデルを入れようとしたけどとりあえず毎回学習するようにする]

# test_data = "了解です！ありがとうございます！"
args = sys.argv
test_data = args[1]


# 実行
wakati_test = wakati.parse(test_data)

# testdataをbinary化
tokenizer = Tokenizer() # またtokenizer作り
tokenizer.fit_on_texts(naivebayes.wakatigaki_all(all))
binary_test = tokenizer.texts_to_matrix([wakati_test], "binary")
# どのcategoryになる確率が一番高いかを計算
p_test = np.zeros((len(p_category),1))
for i in range(len(p_category)):
  p_test[i,0] = p_category[i,0]
  for j in range(len(binary_test[0])):
    if binary_test[0][j] == 1.0:
      p_test[i,0] = p_test[i,0] * p_words[i][j]
    elif binary_test[0][j] == 0.0:
      p_test[i,0] = p_test[i,0] * (1 - p_words[i][j])
# print(all_name[np.argmax(p_test)])
print(np.argmax(p_test))