# コーディング規約

## 目的

本プロジェクトでは、以下を目的としてコーディング規約を定める。

- 可読性の高いソースコードを維持する
- 修正しやすい構造を維持する
- 命名や実装方針のブレを減らす
- 将来的な機能追加やリファクタリングをしやすくする
- 個人開発であっても、実務を意識した開発を行う

本規約は、Phase1 時点では過度に厳格にせず、実装しやすさと保守性のバランスを重視する。

---

## 基本方針

- まずは正しく動くことを重視する
- ただし、動けばよいではなく、読みやすさ・修正しやすさも意識する
- 複雑さを一度に持ち込まず、必要に応じて段階的に改善する
- 同じ責務を複数箇所に分散させない
- 命名で意味が伝わるコードを目指す
- コメントは「何をしているか」ではなく「なぜそうしているか」を優先して書く

---

## コーディングスタイル

### 準拠規格

- 基本的に **PSR-12** に準拠する

---

### 波括弧 `{}` の位置

波括弧 `{` は、対象となる構文の宣言と同じ行に記述する

#### クラス

```php
class SampleClass
{
}
```

#### メソッド

```php
public function handle(): int
{
}
```

#### 制御構文

```php
if ($condition) {
    // ...
}
```

---

### スペースルール

- キーワードと `(` の間に半角スペースを1つ入れる
- `)` と `{` の間に半角スペース1つ入れる
- 演算子の前後には半角スペースを入れる
- カンマの後ろには半角スペースを入れる
- メソッド名と `(` の間にはスペースを入れない

```php
if ($count > 0) {
    $value = $a + $b;
}
```

---

### インデント

- スペース4つで統一する
- タブは使用しない

---

### 空行 （段落）

- 処理の意味が変わる箇所で1行空ける
- 無意味に空行を入れすぎない
- メソッド内は「処理のまとまり」で区切る

---

## 適用範囲

本規約は、主に以下を対象とする。

- PHP
- Laravel の Model / Controller / Command / Service / Seeder
- 設計ドキュメント
- 命名規則
- 実装方針

---

## 命名規則

### クラス名

- `PascalCase` を使用する
- 名詞または名詞句で命名する
- 役割が分かる名前にする

例:

- `Station`
- `Property`
- `PropertyPriceObservation`
- `MarketPriceSeries`
- `AggregateMarketPrice`
- `MarketPriceAggregator`

---

### メソッド名

- `camelCase` を使用する
- 動詞または動詞句で命名する
- 処理内容が分かる名前にする

例:

- `properties()`
- `priceObservations()`
- `marketPriceSeries()`
- `calculatePricePerSqm()`
- `buildGroupKey()`
- `aggregateByMonth()`

避けるべき例:

- `doProcess()`
- `exec()`
- `data()`
- `tmp()`

---

### 変数名

- `camelCase` を使用する
- 省略しすぎない
- 単数 / 複数を正しく使い分ける

例:

- `stationId`
- `propertyType`
- `targetMonth`
- `pricePerSqmList`
- `observations`
- `groupedObservations`

避けるべき例:

- `data`
- `list`
- `tmp`
- `val`
- `obj`

---

### 配列・コレクションの命名

#### 基本

- 複数要素を格納する変数は複数形で命名する
- 単一要素を格納する変数は単数形で命名する

```php
$observation
$observations
$property
$properties
```

#### 取得メソッドに応じた命名

- `first()`, `find()`, `sole()` などで **1件取得する場合は単数形**
- `get()`, `all()` などで **複数件取得する場合は複数形**

```php
$station = Station::query()->first();
$stations = Station::query()->get();

$property = Property::query()->first();
$properties = Property::query()->get();
```

#### 値の一覧

- 特定の値のみの配列は `List` を付ける

```php
$pricePerSqmList
$stationIdList
```

#### 補足

- `List` は「値の一覧」を表すときに使う
- Model の集合には原則として `List` を付けず、複数形で表現する
- `Array` や `Collection` は原則として変数名には含めない

#### NG例

```php
$data
$list
$tmp
$array
$observationList
```

---

### 定数名

- `UPPER_SNAKE_CASE` を使用する
- マジックナンバーや固定文字列を意味のある名前にする

例:

- `PROPERTY_TYPE_MANSION`
- `PROPERTY_TYPE_HOUSE`
- `BUILDING_STRUCTURE_WOOD`
- `DEFAULT_FLOOR_AREA_BAND`

---

### Enum

#### 使用基準

- 値の種類が固定されている
- アプリ全体で使用される
- typoを防ぎたい

---

## 型宣言

- 可能な限り型宣言を行う
- 引数・戻り値の型を明示する
- Eloquent のリレーション戻り値型を明示する
- `casts` を活用して日付や boolean の扱いを明確にする
- nullable は `?` を使用する

例：

```php
public function calculate(int $value): int
{
    return $value * 2;
}
```

---

### リレーションの型

例:

```php
public function station(): BelongsTo
{
    return $this->belongsTo(Station::class);
}
```

---

## ディレクトリ構成

```text
app/
├── Models
├── Http/Controllers
├── Services
├── Enums
├── DTOs
├── Console/Commands

database/
├── migrations
├── seeders
```


### テーブル名

- `snake_case` の複数形を使用する

例:

- `stations`
- `properties`
- `property_price_observations`
- `market_price_series`

---

### カラム名

- `snake_case` を使用する
- 役割が明確になる名前にする
- boolean は `is_` または `has_` を付ける

例:

- `property_type`
- `floor_area_sqm`
- `built_year`
- `has_parking`
- `is_new_build`

---

### リレーションメソッド名

- Laravel の慣例に従う
- 単数関係は単数形
- 複数関係は複数形
- テーブル名ではなく意味のある関係名にする

例:

- `station()`
- `property()`
- `properties()`
- `priceObservations()`
- `marketPriceSeries()`

---

## ファイル名

- クラス名と一致させる
- 1ファイル1クラスを基本とする

例:

- `Station.php`
- `Property.php`
- `PropertyPriceObservation.php`
- `AggregateMarketPrice.php`

---

## 配列の扱い

- 配列は意味の分かるキーを使う
- 同じ構造の配列を何度も書く場合は、メソッド化またはDTO化を検討する
- ネストが深くなりすぎる場合は分割を検討する

例:

```php
[
    'station_id' => $stationId,
    'property_type' => $propertyType,
    'target_month' => $targetMonth,
]
```

避けるべき例:

```php
[
    $stationId,
    $propertyType,
    $targetMonth,
]
```

### 配列キーの命名

- 配列のキーは `snake_case` を使用する
- DBカラム名と一致させることを基本とする

例:

```php
[
    'station_id' => $stationId,
    'property_type' => $propertyType,
    'target_month' => $targetMonth,
]
```

### 理由

- DBカラムと命名を揃えることで可読性が向上する
- 変換処理を減らしバグを防止する
- Laravelの慣習と一致する

---

## メソッド設計

### 基本方針

- 1メソッド1責務を意識する
- 長すぎるメソッドは分割を検討する
- ただし、無理に細かく分割しすぎない
- 処理の流れが追いやすいことを優先する

---

### メソッドの長さ

厳密な行数制限は設けないが、以下を目安とする。

- 20~30行を超えてきたら分割を検討する
- 条件分岐やループが増えて読みづらい場合は分割する
- 同じ処理のまとまりが見える場合はメソッド化する

---

### 引数

- 必要最小限にする
- 意味の分からない引数の並びを避ける
- 引数が増えすぎる場合は配列・DTO・Value Object を検討する

避けるべき例:

```php
createSeries($stationId, $propertyType, $targetMonth, $median, $p25, $p75, $sampleCount);
```

---

## 責務分離

### Model

- データの表現
- リレーション
- そのモデル自身に強く紐づく軽いロジック

例:

- `price_per_sqm` の自動計算
- cast 定義
- リレーション定義

---

### Command

- CLI実行の入口
- 実行開始 / 終了メッセージ
- アプリケーションサービスの呼び出し

できるだけ、複雑な集計ロジックは将来的に Service へ切り出せる構造を意識する。

---

### Service

- 複数モデルにまたがる業務ロジック
- 再利用したい処理
- Controller / Command / Job に直書きしたくない処理

例:

- 月次相場集計
- 帯分類ロジック
- 分位点計算ロジックの整理

---

### Seeder

- 初期データ
- 動作確認用データ
- 開発環境再構築時の再現性を担保するデータ投入

Seeder に業務ロジックを持たせすぎない。

---

## コメント方針

- コメントは必要な箇所のみに書く
- コードを読めば分かる内容はコメントしない
- 「なぜそうしているか」が分かるコメントを優先する

良い例:

```php
// Phase1では全件再集計とし、既存データを一度削除してから再生成する
```

あまり良くない例:

```php
// ループする
foreach (...)
```

---

## 条件分岐

- 条件分岐は浅くすることを意識する
- ガード節を使って早期 return する
- ネストが深くなりすぎる場合はメソッド分割を検討する

例:

```php
if ($observation->property === null) {
    return;
}
```

---

## nullの扱い

- nullable な値にアクセスする前に存在確認を行う
- null を許容する設計かどうかを意識する
- 「なぜ null になり得るか」を設計で理解した上で扱う

---

## Carbon の扱い

### 原則

- Carbon はミュータブル

---

### NG

```php
$date->startOfMonth();
```

---

### OK

```php
$date->copy()->startOfMonth();
```

---

### 理由

- 元データを破壊しないため

---

## 不要な変数

- 使用していない変数は削除する

---

## マジックナンバー / マジック文字列

- 意味のある固定値はベタ書きしない
- 将来的に増える可能性がある値は定数化を検討する

例:

- `mansion`
- `house`
- `wood`
- `steel`
- `rc`
- `all`

ただし Phase1 では、過剰な抽象化は避け、増え始めたら定数化する。

---

## Eloquent の使い方

- リレーションを定義して活用する
- N+1 が発生しそうな箇所では `with()` を検討する
- 取得件数が増える場合は eager loading を意識する

例:

```php
PropertyPriceObservation::query()
    ->with('property')
    ->get();
```

---

## groupBy / 集計キーの扱い

- 複数条件でグルーピングする場合は、意図が分かるように実装する
- キー生成処理が複雑になる場合は専用メソッドに切り出す

例:

```php
private function buildGroupKey(PropertyPriceObservation $observation): string
{
    return implode('|', [
        $observation->property->station_id,
        $observation->property->property_type,
        $observation->observed_on->copy()->startOfMonth()->format('Y-m-d'),
    ]);
}
```

---

## 例外的に意識すること

- 最初から完璧な設計を目指しすぎない
- ただし、後で壊れやすい実装は避ける
- 「今はシンプルに作る」と「雑に作る」は違うことを意識する

---

## Phase1 時点での実装方針

Phase1 では以下を優先する。

- まず動くこと
- 可読性が高いこと
- 修正しやすいこと
- ドキュメントと実装が一致していること

一方で、以下は将来の改善対象としてよい。

- Service クラスへの切り出し
- DTO / Value Object の導入
- より厳密な統計計算
- 機能ごとのブランチ運用
- 詳細な静的解析ルールの導入

---

## 今後導入を検討するもの

- PHP CS Fixer または Laravel Pint
- PHPStan
- 機能ブランチ運用
- PR テンプレート
- Service / Action 層の整理

---

## 補足

本規約は、実装を進めながら必要に応じて更新する。
規約を守ること自体が目的ではなく、保守しやすく品質の高いコードを書くことを目的とする。