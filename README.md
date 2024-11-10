# LaravelとVue3を使ったリアルタイムチャット

# 構成
Docker Desktop  
Laravel sail Framework 11.27.2  
Vue3(Vuetify)
Pusher

# ローカル環境構築手順

### Pusherサイトに登録して各キーを取得

[Pusher](https://pusher.com/)

#### git cloneをする
```
$ git clone https://github.com/k-yonaha/laravel-vue-echo.git
```

#### .env.exampleをコピーする
```
$ cp .env.example .env
```

#### .envの中身を修正
```
# データベース設定
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={hogehoge}
DB_USERNAME={hogehoge}
DB_PASSWORD={hogehoge}

~~~~

PUSHER_APP_ID={Pusherサイトで取得したID}
PUSHER_APP_KEY={Pusherサイトで取得したKEY}
PUSHER_APP_SECRET={Pusherサイトで取得したSECRET}
PUSHER_APP_CLUSTER={Pusherサイトで取得したCLUSTER}
```

#### ライブラリのインストール(composerがない場合はインストールしてください。)
```
$ composer install
```

#### エイリアスの設定
```
$ alias sail='./vendor/bin/sail'
$ source ~/.zshrc
```

#### 起動
```
$ sail up -d
```

#### テーブル作成
```
$ sail artisan migrate
```

#### APP_KEYの生成
```
$ sail artisan key:generate
```

#### パッケージのインストール
```
$ sail npm install
```

#### Vue.js起動
```
$ sail npm run dev
```

#### 画面確認

http://localhost/
にアクセス
