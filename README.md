# 不動産相場可視化アプリ

駅 × 条件（面積帯 / 築年帯 / 物件種別）で
㎡単価中央値の推移を可視化するアプリケーション。

## 技術スタック（予定）

- Laravel 12
- PostgreSQL
- Next.js（TypeScript）
- Docker
- GitHub Actions

## 現在のステータス

Phase1 開発中

### 実装済み

- Docker によるローカル環境構築
- Xdebug によるデバッグ環境構築
- 駅・物件・価格履歴・月次相場集計テーブルの設計・実装
- 物件価格から㎡単価を自動計算する処理
- 月次相場集計コマンド
- 床面積帯・築年帯による相場集計
- 集計済み相場データ取得API
- APIの任意フィルタ
    - floor_area_band
    - built_year_band
- Unitテスト
- Featureテスト

### 今後の予定

- フロントエンド実装
- 相場推移グラフの表示
- APIレスポンスの改善
- CI/CD整備
- READMEへの画面キャプチャ追加
