## ageage　

#### チャット下手な人が上手になるためのチャットアプリ

1. ユーザーとチャットをする。
2. 文章の点数化を行い、点数が低かったら強制的にギャル語に変換され送信される。
3. 自分が送ったチャットの問題点を一覧で見れる。

#### 対象
* 硬い文章を送りがちな上司

#### 機能
- ユーザー登録
- ログイン
- ログアウト
- チャット
- 文章の事前チェック
- 送った文章の結果一覧

## テーブル

##### users
- id
- name
- email
- password
- token
- connection_id
- user_status
- user_image

##### chatinputs
- id
- user_id
- sentence

##### chatoutputs
- id
- user_id
- input_id
- score
- kanji_rate
- emoji_rate
- naive_bayes

##### keigos
- id
- output_id
- keigo

##### posts
- from_user_id    
- to_user_id 　　 
- post_mesage　　 
- message_status　



## Requirements

* [composer](https://getcomposer.org/)
* [Docker](https://docs.docker.com/)

## Installation for developer

Edit environment variables

```bash
cp .env.example .env
```

edit .env
`DB_HOST=mysql`
`DB_USER=karaage`

```bash
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
vendor/bin/sail root-shell -c 'apt-get install -y python3 python3-pip'
vendor/bin/sail shell -c 'pip install -r app/python/requirements.txt'
vendor/bin/sail npm install
vendor/bin/sail npm run dev

```

## Deployment

[Amazon Elastic Beanstalk](https://www.notion.so/Teamspace-Home-57942e4b8f544eb1a367263dc2937018?p=dc2916022c8846ceabbd82c38d3d269b&pm=c)
