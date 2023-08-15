-- Active: 1666592657434@@127.0.0.1@3306@liin
CREATE TABLE marriage (  
    seq BIGINT NOT NULL AUTO_INCREMENT COMMENT '流水編號',
    formcode CHAR(10) NOT NULL COMMENT '表單編號',
    formstate TINYINT NOT NULL DEFAULT 15 COMMENT '表單狀況',
    creator VARCHAR(20) NOT NULL COMMENT '建立者',
    creatdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '建立日期',
    modifier VARCHAR(20) NOT NULL COMMENT '修改者',
    modifydate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT '修改日期',
    mrgcode VARCHAR(20) NOT NULL COMMENT '婚姻狀況代碼',
    mrgapl VARCHAR(300) NOT NULL COMMENT '婚姻狀況名稱',

    INDEX mrg_seq_index(seq),
    INDEX mrg_mrgapl_index(mrgapl),
    PRIMARY KEY (formcode)
) COMMENT '婚姻狀況檔';

INSERT INTO marriage (formcode, creator, modifier, mrgcode, mrgapl) VALUES 
    ('2022100001', '徐培文', '徐培文', 'M001', '單身')
    , ('2022100002', '徐培文', '徐培文', 'M002', '已婚')
    , ('2022100003', '徐培文', '徐培文', 'M003', '未婚')
    , ('2022100004', '徐培文', '徐培文', 'M004', '離婚')
    , ('2022100005', '徐培文', '徐培文', 'M005', '分居')
    , ('2022100006', '徐培文', '徐培文', 'M006', '喪偶')
;