-- Active: 1666592657434@@127.0.0.1@3306@liin
CREATE TABLE liaison_person (  
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號',
    formcode CHAR(10) NOT NULL COMMENT '表單編號',
    formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況',
    creator VARCHAR(20) NOT NULL COMMENT '建立者',
    creatdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '建立日期',
    modifier VARCHAR(20) NOT NULL COMMENT '修改者',
    modifydate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '修改日期',
    liscode VARCHAR(20) NOT NULL COMMENT '聯絡人代碼',
    lisapl VARCHAR(300) NOT NULL COMMENT '聯絡人名稱',

    INDEX lia_seq_index(seq),
    INDEX lia_lisapl_index(lisapl),
    PRIMARY KEY (formcode)
) COMMENT '聯絡人檔';

INSERT INTO liaison_person (formcode, creator, modifier, liscode, lisapl) VALUES 
    ('2022100001', '徐培文', '徐培文', 'L001', '配偶')
    , ('2022100002', '徐培文', '徐培文', 'L002', '兒子')
    , ('2022100003', '徐培文', '徐培文', 'L003', '女兒')
    , ('2022100004', '徐培文', '徐培文', 'L004', '媳婦')
    , ('2022100005', '徐培文', '徐培文', 'L005', '女婿')
    , ('2022100006', '徐培文', '徐培文', 'L006', '兄弟姊妹')
    , ('2022100007', '徐培文', '徐培文', 'L007', '孫子')
    , ('2022100008', '徐培文', '徐培文', 'L008', '孫女')
    , ('2022100009', '徐培文', '徐培文', 'L009', '父親')
    , ('2022100010', '徐培文', '徐培文', 'L010', '母親')
    , ('2022100011', '徐培文', '徐培文', 'L011', '堂表兄弟姊妹')
    , ('2022100012', '徐培文', '徐培文', 'L012', '朋友')
    , ('2022100013', '徐培文', '徐培文', 'L013', '鄰居')
    , ('2022100014', '徐培文', '徐培文', 'L014', '其他親戚')
    , ('2022100015', '徐培文', '徐培文', 'L015', '其他')
    , ('2022100016', '徐培文', '徐培文', 'L016', '看護')
;