最基本的 PHP 測試
================

## 前言

這篇 PHP 測試教學沒有使用任何 PHP 框架，它僅有兩個非常重要的檔案： `PostController.php` 及 `PostModel.php`。
這份專案並不是一個完整的後端專案，它沒有實作完整的 API、也沒有渲染任何畫面，但它已足夠指導你該如何入門 unit test，特別是針對 MVC 架構的程式碼。

## 使用工具

1. composer: 負責管理使用的套件
2. phpunit: PHP 的測試工具
3. mockery: PHP 的測試工具，負責生成 mock object，降低你測試的程式碼依賴性

## 安裝

1. 請確認你的電腦裡已安裝 composer
2. git clone 此專案
3. `$ composer install`
4. `$ composer dump`
5. 若要進行測試，請於終端輸入 `./vendor/bin/phpunit src/tests`

## 專案架構說明

```
└── src
    └── Controllers
        └── PostController.php
    └── Models
        └── PostModel.php
    └── tests
        └── PostControllerTest.php
        └── PostModelTest.php
```

1. `PostModel`: 負責跟資料庫溝通，從資料庫中提取 posts 資料。這邊為求簡潔資料庫以 array 替代。
2. `PostController`: 依賴 `PostModel`，透過 `PostModel` 獲得或儲存資料。負責所有 posts 的業務邏輯 (新增、更新、刪除文章)。為求簡潔這邊僅示範幾個功能，剩下功能只要依此類推就能實作出來。由於 `PostController` 依賴 `PostModel`，所以這邊使用依賴注入，將 `PostModel` 作為 controller 的輸入參數帶入。
3. `PostModelTest`: 測試 `PostModel` 的檔案，裡面包含兩個測試函數：`test_insert`, `test_show`。
3. `PostControllerTest`: 測試 `PostController` 的檔案，由於 `PostController` 依賴 `PostModel`，`PostModel` 此時需要被 mocked，避免 model 內的程式碼影響結果。

## 測試觀念說明

### 命名規則

1. 測試檔案的命名：一般來說你想測什麼 class，測試檔名就是 className + Test。
2. 測試函數的命名：測試函數一定要以 `test_` 開頭，否則執行測試時這個函數會被跳過。例如想測試 getDate()，測試函數就會命名為 `test_getDate`。

### 輔助函數

1. setUp：每當一個測試函數執行前都會執行的函數，`PostModel` 有兩個測試函數所以它總共會執行兩遍。注意你一定要寫 `public function setUp(): void`，這幾個字，一個字都不能少不然會跳出錯誤。
2. tearDown：每當一個測試函數執行完後執行的函數。通常用在刪除 mock 物件。
3. 還有其他輔助函數可以參考官方文件，或是看看[這個](https://dyclassroom.com/phpunit/phpunit-fixtures-setup-and-teardown)。

### unit test

1. unit test: 指針對一個函數測試它的輸出結果是否符合預期。
2.  3A 原則：一個 unit test 測試函數裡基本核心三個步驟 —— Arrange, Act, Assert。分別代表「決定測資」、「執行函數」、「比對結果」。
3. 函數最小化：如果你的函數裡有 40 行，跑測試時它測試失敗，你很難找出出錯的地方；如果你的函數使用超多外部類別或函數，寫測試函數時會很痛苦 (經驗談)；如果你的函數一次做三件工作：更新資料、另存檔案、寄送提醒訊息，未來的你想回頭瀏覽這個函數到底在做什麼會非常痛苦，更別提你想修改這個函數的話會是地獄級痛苦。所以要養成好習慣一個函數不要寫太長、並讓它只專職一個工作。
4. 無法追蹤一個函數的中間過程：  
    假如你有一個函數長這樣：
    ```
    function setText() {
        $a = A();
        $b = "B" . getC() . $a;

        return toUpperCase($b);
    }
    ```
    `$b` 看起來是個很複雜的東西，但你很難透過測試函數確認 `$b` 在運行過程中值的變化。因為 unit test 本身設計來監測「回傳值」。所以我們只能比對 `toUpperCase($b)` 是否符合預期，$b 長什麼樣子幾乎不得而知。如果你想確認 $b 的結果，你得把 `"B" . getC() . $a;` 包裝成一個函數。
    ```
    function setText() {
        $a = A();
        $b = getB($a);

        return toUpperCase($b);
    }

    function getB($a) {             // <-- 可以測試 getB() 是否符合預期
        return "B" . getC() . $a;
    }
    ```

### mock & stub (偽裝)

4. mock: 當你測試 PostController 時，雖然它使用到了 PostModel，但我們只能由 controller 內的程式碼決定測試結果是否通過，model 內的程式碼不論如何運作，都不能影響到測試結果。所以測試時須跳過 model 的實作內容，直接「偽裝」一個 model 的輸出值，並讓 controller 使用這個偽裝值繼續執行下去。
5. mock vs stub：這兩個東西都是「偽裝」，差異在於使用的時機不同。Stub 用來偽裝比較「簡單」的物件，如果你要偽裝的東西是 int, string, function, ... 這些原生類型，那就使用 stub；如果你要偽裝一個 custom class ，或任何第三方 class, function, library 等比較「複雜」的東西，就使用 mock。
6. 依賴注入：若 A class 會使用到 B class 內的 methods 或 property，我們習慣把 B 當作 A 的建構子參數帶入。這麼做的理由有兩個：
   1. **提高 class 的延展性：** 假如 A 是 sendMessage class，B 是 Email class，A(B) 代表傳遞電子信件功能。未來你需要實作「傳遞簡訊」功能時，只需要新增一個 C class 叫 TextMessage，A(C) 就是傳遞簡訊功能，你不需再多寫一次類似的業務邏輯。
   2. **方便測試時使用 mock：** 由於 mock 本身的設計，mock 一個函數的輸入參數很簡單；但在函數內部實例化的物件很難被 mock。


## 外部資源

1. [autoload.php 是什麼](https://jsnwork.kiiuo.com/archives/2618/php-composer-%E9%9D%9E%E5%B8%B8%E7%B0%A1%E5%96%AE%E7%9A%84%E4%BD%BF%E7%94%A8-psr-4-%E4%BE%86%E5%BB%BA%E7%AB%8B%E8%87%AA%E5%8B%95%E8%AE%80%E5%8F%96%E9%A1%9E%E5%88%A5/)
2. [如何用 composer 安裝並使用 phpunit](https://phpunit.de/getting-started/phpunit-9.html)
3. [如何測試 Laravel Request](https://zieger.tw/phpunit-laravel-controller/)