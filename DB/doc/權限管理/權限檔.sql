CREATE TABLE IF NOT EXISTS pms(
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號'
    , formcode CHAR(10) NOT NULL PRIMARY KEY COMMENT '表單編號'
    , formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況'
    , creator VARCHAR(20) NOT NULL DEFAULT 'SYSTEM' COMMENT '建立者'
    , creatdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '建立日期'
    , modifier VARCHAR(20) NOT NULL DEFAULT 'SYSTEM' COMMENT '修改者'
    , modifydate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '修改日期'
    , empformcode CHAR(10) NOT NULL COMMENT '員工檔表單編號'
    , empapl VARCHAR(20) NOT NULL COMMENT '員工檔員工姓名'
    , empcode VARCHAR(20) NOT NULL COMMENT '員工檔員工編號'
    , prgformcode CHAR(10) NOT NULL COMMENT '程式檔表單編號'
    , prgcls VARCHAR(50) NOT NULL COMMENT '程式檔程式分類'
    , enabcrt TINYINT NOT NULL DEFAULT 0 COMMENT '啟用建立功能'
    , enabdic TINYINT NOT NULL DEFAULT 0 COMMENT '啟用註銷功能'
    , enabedit TINYINT NOT NULL DEFAULT 0 COMMENT '啟用編輯功能'
    , enabqry TINYINT NOT NULL DEFAULT 0 COMMENT '啟用查詢功能'
    , enabprt TINYINT NOT NULL DEFAULT 0 COMMENT '啟用列印功能'
    , enabvrf TINYINT NOT NULL DEFAULT 0 COMMENT '啟用審核功能'
    , INDEX pms_seq_idx(seq)
    , INDEX pms_empformcode_idx(empformcode)
    , INDEX pms_empapl_idx(empapl)
    , INDEX pms_empcode_idx(empcode)
    , INDEX pms_prgformcode_idx(prgformcode)
    , INDEX pms_prgcls_idx(prgcls)
);