-- auto-generated definition
create table user_cwl_extended_account_data
(
 ucad_id        int auto_increment                  primary key,
 user_id        int                                 not null,
 puid           varchar(50)                         not null,
 ubc_role_id    int                                 not null,
 ubc_dept_id    int                                 not null,
 CWLLogin       varchar(50)                         not null,
 CWLNickname    varchar(150)                        not null,
 CWLRole        varchar(120)                        not null,
 CWLSaltedID    varchar(200)                        not null,
 wgDBprefix     varchar(150)                        not null,
 date_created   timestamp default CURRENT_TIMESTAMP not null,
 account_status varchar(10)                         not null
);