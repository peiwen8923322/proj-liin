<!--  Nav區塊 下拉選單  -->
<nav>
    <div class="row" style="background-color: #3E7050;">
        <ul class="nav nav-tabs">
            <div class="col-sm-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" aria-current="page" href="../../Apps/index/mindex.php">首頁</a>
                </li>
            </div>
            
            <div class="col-sm-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">人事管理</a>
                    <ul class="dropdown-menu">
                        <li><a href="../chgPaword/pawordChg.php" class="dropdown-item" target="_self">變更密碼</a></li>
                        <li><div class="dropdown-divider"></div></li>

                        <li><a href="../emp/empNew.php" class="dropdown-item" target="_self">建立員工資料</a></li>
                        <li><a href="../emp/empQuery.php" class="dropdown-item" target="_self">維護員工資料</a></li>
                        <li><a href="#" class="dropdown-item" target="_self">教育訓練(目前沒有畫面)</a></li>
                        <li><div class="dropdown-divider"></div></li>

                        <li><a href="../permissions/permissionsNew.php" class="dropdown-item" target="_self">建立員工權限</a></li>
                        <li><a href="../permissions/permissionsQuery.php" class="dropdown-item" target="_self">維護員工權限</a></li>
                        <li><div class="dropdown-divider"></div></li>
                        
                        <li><a href="../WorkOverTime/workOverTimeNew.php" class="dropdown-item" target="_self">建立員工加班資料(程式未建置)</a></li>
                        <li><a href="../WorkOverTime/workOverTimeQuery.php" class="dropdown-item" target="_self">維護員工加班資料(程式未建置)</a></li>
                        <li><a href="../WorkOverTime/workOverTimeExamine.php" class="dropdown-item" target="_self">審核員工加班資料(程式未建置)</a></li>
                        <li><div class="dropdown-divider"></div></li>
                        <li><a href="#" class="dropdown-item" target="_self">薪資管理(目前沒有畫面)</a></li>
                    </ul>
                </li>
            </div>
            
            <div class="col-sm-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">庫存管理</a>
                    <ul class="dropdown-menu">
                        <li><a href="../suppliers/supplierNew.php" class="dropdown-item" target="_self">建立供應商資料</a></li>
                        <li><a href="../suppliers/supplierQuery.php" class="dropdown-item" target="_self">維護供應商資料</a></li>
                        <li><div class="dropdown-divider"></div></li>
                        <li><a href="../materials/materialsNew.php" class="dropdown-item" target="_self">建立品項資料</a></li>
                        <li><a href="../materials/materialsQuery.php" class="dropdown-item" target="_self">維護品項資料</a></li>
                        <li><div class="dropdown-divider"></div></li>
                        <li><a href="../checkStocks/checkStocksNew.php" class="dropdown-item" target="_self">建立盤點資料</a></li>
                        <li><a href="../checkStocks/checkStocksQuery.php" class="dropdown-item" target="_self">維護盤點資料</a></li>
                        <li><div class="dropdown-divider"></div></li>
                        <li><a href="../purchaseOrders/purchaseOrdersNew.php" class="dropdown-item" target="_self">建立進貨資料</a></li>
                        <li><a href="../purchaseOrders/purchaseOrdersQuery.php" class="dropdown-item" target="_self">維護進貨資料</a></li>
                        <li><div class="dropdown-divider"></div></li>
                        <li><a href="../receivingMaterial/receivingMaterialNew.php" class="dropdown-item" target="_self">建立領料資料</a></li>
                        <li><a href="../receivingMaterial/receivingMaterialQuery.php" class="dropdown-item" target="_self">維護領料資料</a></li>
                        
                        
                        <!--
                        <li><div class="dropdown-divider"></div></li>
                        <li><a href="../suppliers/supplierQuery.php" class="dropdown-item" target="_self">供應商管理(未建置)</a></li>
                        -->
                    </ul>
                </li>
            </div>

            <div class="col-sm-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">刷卡管理</a>
                    <ul class="dropdown-menu">
                        <li><a href="../ClockIn/ClockInNew.php" class="dropdown-item" target="_self">建立刷卡資料</a></li>
                        <li><a href="../ClockIn/ClockInQuery.php" class="dropdown-item" target="_self">查詢刷卡資料</a></li>
                        <li><a href="../ClockIn/ClockInHsty.php" class="dropdown-item" target="_self">查詢刷卡歷史資料</a></li>
                        <li><a href="../ClockIn/ClockInYearStatistic.php" class="dropdown-item" target="_self">統計刷卡資料</a></li>
                    </ul>
                </li>
            </div>

            <div class="col-sm-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">請假管理</a>
                    <ul class="dropdown-menu">
                        <li><a href="../holidays/holidaysNew.php" class="dropdown-item" target="_self">建立員工請假資料</a></li>
                        <li><a href="../holidays/holidaysQuery.php" class="dropdown-item" target="_self">維護員工請假資料</a></li>
                        <li><a href="../holidays/holidaysExamine.php" class="dropdown-item" target="_self">審核員工請假資料</a></li>
                        <li><a href="../holidays/holidaysHsty.php" class="dropdown-item" target="_self">查詢員工請假歷史資料</a></li>
                        <li><a href="../holidays/holidaysYearStatistic.php" class="dropdown-item" target="_self">統計員工請假資料</a></li>
                    </ul>
                </li>
            </div>

            <!-- 
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">外出/加班</a>
                <ul class="dropdown-menu">
                    <li><a href="../holidays/holidaysNew.php" class="dropdown-item" target="_self">建立員工請假資料</a></li>
                    <li><a href="../holidays/holidaysQuery.php" class="dropdown-item" target="_self">維護員工請假資料</a></li>
                    <li><a href="../holidays/holidaysExamine.php" class="dropdown-item" target="_self">審核員工請假資料</a></li>
                    <li><a href="../holidays/holidaysHsty.php" class="dropdown-item" target="_self">查詢員工請假歷史資料</a></li>
                    <li><a href="../holidays/holidaysYearStatistic.php" class="dropdown-item" target="_self">統計員工請假資料</a></li>
                </ul>
            </li>
            -->

            

            <!-- 
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">個案管理</a>
                <ul class="dropdown-menu">
                    <li><a href="#" class="dropdown-item" target="_self">個案資料管理</a></li>
                    <li><a href="#" class="dropdown-item" target="_self">個案服務項目管理</a></li>
                    <li><a href="#" class="dropdown-item" target="_self">個案帳務管理</a></li>
                </ul>
            </li>
            -->

            <!--
            <li class="nav-item">
                <a class="nav-link text-white" aria-current="page" href="#">居服員管理</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" aria-current="page" href="#">Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#">Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
            </li>
            -->
        </ul>
    </div>
</nav>