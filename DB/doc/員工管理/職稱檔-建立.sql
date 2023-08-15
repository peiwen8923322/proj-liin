-- Active: 1666592657434@@127.0.0.1@3306@liin
CREATE TABLE positions (  
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號',
    formcode CHAR(10) NOT NULL COMMENT '表單編號',
    formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況',
    creator VARCHAR(20) NOT NULL COMMENT '建立者',
    creatdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '建立日期',
    modifier VARCHAR(20) NOT NULL COMMENT '修改者',
    modifydate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '修改日期',
    poscode VARCHAR(20) NOT NULL COMMENT '職稱代碼',
    posapl VARCHAR(300) NOT NULL COMMENT '職稱名稱',

    INDEX pos_seq_index(seq),
    INDEX pos_posapl_index(posapl),
    PRIMARY KEY (formcode)
) COMMENT '職稱檔';

INSERT INTO positions (formcode, creator, modifier, poscode, posapl) VALUES 
    ('2022100001', '徐培文', '徐培文', 'P001', '主任')
    , ('2022100002', '徐培文', '徐培文', 'P002', '業務負責人')
    , ('2022100003', '徐培文', '徐培文', 'P003', '會計')
    , ('2022100004', '徐培文', '徐培文', 'P004', '人事')
    , ('2022100005', '徐培文', '徐培文', 'P005', '總務')
    , ('2022100006', '徐培文', '徐培文', 'P006', '護理人員')
    , ('2022100007', '徐培文', '徐培文', 'P007', '復能老師')
    , ('2022100008', '徐培文', '徐培文', 'P008', '呼吸治療師')
    , ('2022100009', '徐培文', '徐培文', 'P009', '社工組長')
    , ('2022100010', '徐培文', '徐培文', 'P010', '社工')
    , ('2022100011', '徐培文', '徐培文', 'P011', '個管師')
    , ('2022100012', '徐培文', '徐培文', 'P012', '督導')
    , ('2022100013', '徐培文', '徐培文', 'P013', '照顧服務員')
    , ('2022100014', '徐培文', '徐培文', 'P014', '司機')
    , ('2022100015', '徐培文', '徐培文', 'P015', '廚師')
    , ('2022100016', '徐培文', '徐培文', 'P016', '清潔人員')
    , ('2022100017', '徐培文', '徐培文', 'P017', '行政人員')
    , ('2022100018', '徐培文', '徐培文', 'P018', '資訊')
    , ('2022100019', '徐培文', '徐培文', 'P019', '行銷企劃')
    , ('2022100020', '徐培文', '徐培文', 'P020', '志工')
    , ('2022100021', '徐培文', '徐培文', 'P021', '其他')
;