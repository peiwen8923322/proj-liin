CREATE TABLE clockin(
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號'
    , index clockin_seq_idx(seq)
    , formcode CHAR(10) NOT NULL COMMENT '表單編號'
    , PRIMARY KEY(formcode)
    , formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況'
    , creator VARCHAR(60) NOT NULL DEFAULT 'SYSTEM' COMMENT '建立者'
    , creatdate DATETIME NOT NULL DEFAULT current_timestamp() COMMENT '建立日期'
    , modifier VARCHAR(60) NOT NULL DEFAULT 'SYSTEM' COMMENT '修改者'
    , modifydate DATETIME NOT NULL DEFAULT current_timestamp() COMMENT '修改日期'
    , deptspk CHAR(10) NOT NULL COMMENT '機構部門檔主鍵'
    , cmpcode VARCHAR(20) NOT NULL COMMENT '機構代碼'
    , cmpapl VARCHAR(300) NOT NULL COMMENT '機構名稱'
    , index clockin_cmpapl_idx(cmpapl)
    , empformcode CHAR(10) NOT NULL COMMENT '員工檔主鍵'
    , empapl VARCHAR(20) NOT NULL COMMENT '員工檔員工姓名'
    , empcode VARCHAR(20) NOT NULL COMMENT '員工檔員工編號'
    , year INT NOT NULL COMMENT '年度(西元年)'
    , clkintime DATETIME NOT NULL DEFAULT current_timestamp() COMMENT '刷卡時間'
    , isnormality VARCHAR(30) NOT NULL DEFAULT '正常' COMMENT '刷卡是否正常'
    , index clockin_isnormality_idx(isnormality)
    , clkinsttpk VARCHAR(10) NOT NULL COMMENT '刷卡狀態唯一識別碼'
    , clkinsttcode VARCHAR(60) NOT NULL COMMENT '刷卡狀態編號'
    , clkinsttapl VARCHAR(300) NOT NULL COMMENT '刷卡狀態名稱'
    , extodnymemo TEXT NULL COMMENT '異常說明'
) COMMENT '刷卡檔', AUTO_INCREMENT=1;
