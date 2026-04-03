# Debug Kit

DebugKit は CakePHP アプリケーション向けのデバッグツールバーと拡張デバッグツールを提供します。設定データ、ログメッセージ、SQL クエリー、実行時間情報をすばやく確認できます。

::: warning
DebugKit は単一ユーザーのローカル開発環境でのみ使用してください。共有開発環境、ステージング環境、本番に近い環境では使用しないでください。
:::

## インストール

アプリケーションのルートディレクトリーで次を実行します。

```bash
php composer.phar require --dev cakephp/debug_kit:"^5.0"
```

続いてプラグインを有効化します。

```bash
bin/cake plugin load DebugKit --only-debug
```

## 設定

* `DebugKit.panels` で各パネルの有効化と無効化ができます。
* `DebugKit.includeSchemaReflection` を `true` にするとスキーマリフレクションのクエリーを記録します。
* `DebugKit.safeTld` でローカル開発用の TLD を追加できます。
* `DebugKit.forceEnable` で DebugKit を強制表示できます。

## データベース設定

デフォルトでは `tmp` ディレクトリー内の SQLite データベースにパネルデータを保存します。`pdo_sqlite` が使えない場合は、`config/app.php` に `debug_kit` コネクションを定義してください。

## ツールバーの使い方

DebugKit ツールバーはブラウザー右下の CakePHP アイコンから開けます。各パネルはアプリケーションの異なる側面を表示します。

* **Cache** キャッシュ使用状況の確認と削除。
* **Environment** PHP と CakePHP の環境情報。
* **History** 過去のリクエスト一覧とそのデータの再表示。
* **Include** 読み込まれたファイル一覧。
* **Log** リクエスト中に書かれたログ。
* **Packages** 依存パッケージとバージョン情報。
* **Mail** 送信メールの確認とプレビュー。
* **Request** 現在のリクエスト情報、ルート、Cookie。
* **Session** セッション内容。
* **Sql Logs** 接続ごとの SQL ログ。
* **Timer** `DebugKit\\DebugTimer` と `DebugKit\\DebugMemory` の情報。
* **Variables** ビュー変数。

## 履歴パネルを使う

履歴パネルではエラーやリダイレクトを含む過去のリクエストを確認できます。

![履歴パネルのスクリーンショット](/history-panel.png)

過去のデータが読み込まれると、パネルタイトルが切り替わり、現在のリクエストではないことが分かります。

<video controls preload="metadata" src="/history-panel-use.mp4"></video>

## メールパネルを使う

メールパネルではリクエスト中に送信されたすべてのメールを確認できます。

<video controls preload="metadata" src="/mail-panel.mp4"></video>

メールプレビューを使うと、送信前に内容を確認できます。

<video controls preload="metadata" src="/mail-previewer.mp4"></video>

### プレビュークラスの作成

```php
namespace App\Mailer\Preview;

use DebugKit\Mailer\MailPreview;

class WelcomePreview extends MailPreview
{
    public function welcome(): \Cake\Mailer\Mailer
    {
        $mailer = $this->getMailer('Welcome');

        return $mailer;
    }
}
```

`MailPreview` クラスは `Mailer\Preview` 名前空間に配置し、クラス名は `Preview` で終える必要があります。

## 独自パネルの開発

### パネルクラスを作成する

パネルクラスは `src/Panel` に配置し、`DebugPanel` を継承します。

```php
namespace App\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    // ...
}
```

### コールバック

デフォルトでは `Controller.initialize` と `Controller.shutdown` を購読します。追加イベントが必要なら `implementedEvents()` を定義してください。

### パネル要素

パネル表示用のビュー要素を用意します。名前はクラス名のアンダースコア形式です。例えば `SessionPanel` は `session_panel.php` を使います。

### カスタムタイトルとエレメント

* `title()` はツールバー上のタイトルを設定します。
* `elementName()` は使用するエレメント名を設定します。

### パネルフックメソッド

* `shutdown(Event $event)` はデータを収集します。
* `summary()` は折りたたみ時のサマリー文字列を返します。
* `data()` はシリアライズ可能なパネルデータを返します。

### 他のプラグインのパネル

```php
namespace MyPlugin\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    public string $plugin = 'MyPlugin';
}
```

アプリケーションまたはプラグインのパネルを追加するには、DebugKit 設定を更新します。

```php
Configure::write('DebugKit.panels', ['App', 'MyPlugin.MyCustom']);
$this->addPlugin('DebugKit', ['bootstrap' => true]);
```

## ヘルパー関数

* `sql()` は ORM クエリーの SQL をダンプします。
* `sqld()` は ORM クエリーの SQL をダンプして終了します。
