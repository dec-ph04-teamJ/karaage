import sys
import pandas as pd
import MeCab
import re
import emoji
import pickle

args = sys.argv

def str_to_dataframe(test_str):
  df=pd.DataFrame([test_str],columns=["内容"])
  return df

def get_feature_value(df):
  lis=[]
  keigo_lis=["御","成る","ます","教示","下さる","沙汰","僭越","為さる","頂く","おっしゃる","れる","申す","いらっしゃる",
            "出でる","窺う","参る","御覧","拝見","拝聴","申し伝える","思し召す","存ずる","拝受する","賜わる","召し上がる",
            "差し上げる","です"]
  for index,content in zip(df.index,df["内容"]):
    str_amount=len(content)
    #文字列の長さ抽出
    emoji_parcent=emoji.emoji_count(content)/str_amount
    #文字列に対する絵文字の割合を出す。
    kanji_parcent=len(re.findall('[\u4E00-\u9FD0]', content))/str_amount
    #文字列に対する漢字の割合を出す。
    tagger=MeCab.Tagger()
    words=tagger.parse(content).split("\n")
    #一行ごとに形態素解析。splitをすることで各単語に分けることができる。
    words=words[:-2]
    #いらない要素があるので消す
    keigo_count=0
    for word in words:
      keigo_check=word.split("\t")[3]
      #3番目の要素に比較する敬語かどうか比較する要素が入っている
      if keigo_check in keigo_lis:
        #敬語がまとめてあるリストと要素を比較する。
        keigo_count+=1
      else:
        continue
    keigo_parcent=keigo_count/len(words)
    lis.append([kanji_parcent,keigo_parcent,emoji_parcent])
    df_kyousi=pd.DataFrame(lis,columns=["漢字の割合","敬語の割合","絵文字の割合"])
  df=pd.concat([df, df_kyousi], axis=1)
  return df  

def get_score(df):
  test_x=df[["漢字の割合","絵文字の割合","敬語の割合"]]
  with open('storage/model.pickle','rb') as f:
    lr= pickle.load(f)
  y_pred_prob=lr.predict_proba(test_x)
  score=y_pred_prob[0][1]*100
  return score


df=str_to_dataframe(args[1])
df=get_feature_value(df)
score=get_score(df)

print(score)


