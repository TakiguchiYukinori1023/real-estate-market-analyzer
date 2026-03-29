# DB設計

## 設計方針

Phase1では、生データと集計データを分離する。

- 生データ
  - `properties`
  - `property_price_observations`

- 集計データ
  - `market_price_series`

これにより、以下を実現しやすくする。

- 相場計算ロジックの変更
- 月次集計の再生成
- 将来的な分析機能追加
- APIレスポンスの高速化

---

### stations

駅マスタ。

### 主な役割

- 物件の所属先
- 相場集計の基準単位

### 主なカラム

| column | type | description |
|---|---|---|
| id | bigint | 主キー |
| line_name | string | 路線名 |
| station_name | string | 駅名 |
| display_name | 画面表示用の名称 |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### 制約

- `unique(line_name, station_name)`
  - 路線名 + 駅名の重複登録を防止する

### 補足

- `display_name` は UI 表示用に保持する
- 例: `JR中央線 中野`

---

## properties

物件の基本情報を保持するテーブル。

### 主な役割

- 相場算出のための物件属性を持つ
- 価格観測履歴の親テーブルになる

### 主なカラム

| column | type | description |
|---|---|---|
| id | bigint | 主キー |
| station_id | bigint | `stations.id` への外部キー |
| property_type | string | 物件種別。Phase1では `mansion` / `house` |
| floor_area_sqm | decimal | 延床面積（㎡） |
| built_year | smallint | 建築年 |
| walk_minutes | smallint | 最寄り駅までの徒歩分 |
| building_structure | string | 建物構造。`wood` / `steel` / `rc` を想定 |
| has_parking | boolean | 駐車場の有無 |
| is_new_build | boolean | 新築か中古か |
| management_fee_yen | integer | 管理費（月額）。マンションの場合のみ設定される |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### 補足

- 戸建ては Phase1 では延床面積ベースで扱う
- `built_age` で入力された値は `built_year` に変換して保存する
- `walk_minutes` は Phase1 では任意入力とする
- `is_new_build` は価格形成に大きな影響を与えるため Phase1 から保持する
- management_fee_yen はマンション特有の属性であり、戸建ての場合は null とする

---

## property_price_observations

物件価格の観測履歴を保持するテーブル

### 主な役割

- 相場計算の元データ
- 時系列での市場観測を保持する
- 同一物件の掲載価格変化を保持する

### 主なカラム

| column | type | description |
|---|---|---|
| id | bigint | 主キー |
| property_id | bigint | `properties.id` への外部キー |
| observed_on | date | 価格を観測した日付 |
| price_yen | bigint | 観測時点の価格（円） |
| price_per_sqm | decimal | ㎡単価。`price_yen / floor_area_sqm` |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### 補足

- 1物件に対して複数日の価格を保持できる
- Phase1では `price_per_sqm` を保存することで検索・集計を簡単にする
- このテーブルは「値下げ履歴」専用ではなく、市場観測データの蓄積として扱う

---

### market_price_series

駅ごとの月次相場集計データを保持するテーブル

### 主な役割

- 相場グラフ表示用データ
- APIレスポンスの高速化
- 将来の分析基盤

### 主なカラム

| column | type | description |
|---|---|---|
| id | bigint | 主キー |
| station_id | bigint | `stations.id` への外部キー |
| property_type | string | 物件種別。`mansion` / `house` |
| floor_area_band | string | 面積帯。例: `60-80` |
| built_year_band | string | 築年帯。例: `2010-2019` |
| target_month | date | 集計対象月の先頭日 |
| median_price_per_sqm | bigint | ㎡単価中央値 |
| p25_price_per_sqm | bigint | 25パーセンタイル |
| p75_price_per_sqm | bigint | 75パーセンタイル |
| sample_count | integer | 集計対象件数 |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### 補足

- Phase1では月次固定とする
- Phase1では徒歩分帯は持たない
- 面積帯・築年帯は文字列で保持してシンプルに実装する

---

## インデックス設計

### stations

- `unique(line_name, station_name)`
  - 駅の重複登録防止
  - 路線 + 駅名での一意性を担保する

### properies

- `index(station_id)`
  - 駅ごとの物件検索を高速化する
- `index(property_type)`
  - 物件種別での絞り込みに利用する
- `index(station_id, property_type)`
  - 駅 × 物件種別での検索を高速化する

### property_price_observations

- `index(property_id)`
  - 物件ごとの観測履歴取得に利用する
- `index(observed_on)`
  - 観測日での絞り込みや月次集計に利用する
- `unique(property_id, observed_on)`
  - 同一物件・同一日の二重登録を防止する

### market_price_series

- `index(station_id)`
  - 駅単位の相場取得に利用する
- `index(target_month)`
  - 月次の時系列取得に利用する
- `index(station_id, property_type, target_month)`
  - 駅 × 物件種別 × 月の相場取得を高速化する

---

## Phase1で扱わないもの

以下は将来拡張とし、Phase1では扱わない。

- 土地面積
- 公道 / 私道
- 方角
- 所有権 / 賃借権
- 前面道路幅員
- 角地
- セットバック有無
- 再建築可否
- 金利時系列データ

---

## 将来拡張

Phase1では、相場可視化に必要な最小限の物件属性のみを扱う。
将来的には、より条件が近い物件群の価格変動を分析できるように、物件属性および外部指標を拡張する想定とする。

### properties に将来追加を想定する項目

以下は、物件そのものの属性として `properties` に追加する想定である。

- 土地面積
- 用途地域
- 建蔽率
- 容積率
- 所有権 / 賃借権
- 前面道路幅員
- 公道 / 私道
- 接道道路数
- 方角
- 角地
- セットバック有無
- 再建築可否

これらは特に戸建てや土地価格の形成に影響を与える要素であり、
Phase1ではスコープを絞るために除外するが、将来的には `properties` の属性として保持することで、
より類似条件の物件群を対象とした相場分析が可能になる。

### properties に追加済みの拡張項目

Phase1では、将来分析に向けた最小限の拡張として以下を保持する。

- `building_structure`
  - 建物構造。`wood` / `steel` / `rc` を想定する
- `has_parking`
  - 駐車場の有無
- `is_new_build`
  - 新築か中古かを表す

### 別テーブルで管理する想定の項目

以下は物件固有の属性ではなく、地域や地点、または市場全体に紐づく指標であるため、
`properties` には直接保持せず、将来的には独立したテーブルで管理する想定とする。

- 路線価
- 公示地価
- 基準地価
- 金利時系列データ
- ハザードマップ情報
  - 洪水、土砂災害、津波、液状化などの災害リスク情報
  - 物件固有属性ではなく位置情報に紐づく外部データとして、将来的には別テーブルまたは外部連携で管理する想定とする

これらは年次または月次で変動しうる外部データであり、
物件属性と分離することで設計の責務を明確にし、将来的な分析拡張をしやすくする。

## 集計データ更新方針

Phase1では、`market_price_series` は手動再集計とする。
生データである `property_price_observations` を元に、月次の相場集計を全件再生成する。

将来的には、Laravel Scheduler や Job を用いた定期バッチ実行に拡張する想定とする。