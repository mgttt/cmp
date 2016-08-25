關於多語言

我們的多語言策略是：
一、用 XML格式 的excel5 (office 2003)文件
來獲得一個 二維展開的 語言包 數據結構
來獲得直觀的維護效率！
二、編輯後的XML拖入.zip
生產環境只發佈 .zip節約流量與空間
三、on-demand-build 算法
當需要用到多語言如
getLang()
或模板中 {I18N_XXXX}
時，去 _TMP_ 找有否編譯後的 php，如果沒有就把
lang_pack.xls.zip展開到 _TMP_/
再把 xml 編譯為 php數組，實現 on-demain-build 算法。
從而在直觀維護的基礎上獲得接近原生php編輯的高效率.
