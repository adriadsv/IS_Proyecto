/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     21.01.2026 00:04:10                          */
/*==============================================================*/


alter table COMPRAS
   drop foreign key FK_COMPRAS_PRV_COMPR_PROVEEDO;

alter table DETALLE_CARRITO
   drop foreign key FK_DETALLE__PRD_DET_C_PRODUCTO;

alter table FACTURAS
   drop foreign key FK_FACTURAS_CLI_FACTU_CLIENTES;

alter table KARDEX
   drop foreign key FK_KARDEX_BOD_KARD_BODEGAS;

alter table KARDEX
   drop foreign key FK_KARDEX_CMP_KAR_COMPRAS;

alter table KARDEX
   drop foreign key FK_KARDEX_FAC_KAR_FACTURAS;

alter table KARDEX
   drop foreign key FK_KARDEX_PRO_KARD_PRODUCTO;

alter table KARDEX
   drop foreign key FK_KARDEX_TRN_KAR_TRANSACC;

alter table PRODUCTOS
   drop foreign key FK_PRODUCTO_CAT_PROD_CATEGORI;

alter table PROXBOD
   drop foreign key FK_PROXBOD_BOD_PROXB_BODEGAS;

alter table PROXBOD
   drop foreign key FK_PROXBOD_PROD_DET__PRODUCTO;

alter table PROXCMP
   drop foreign key FK_PROXCMP_CMP_PROXC_COMPRAS;

alter table PROXCMP
   drop foreign key FK_PROXCMP_PRD_DET_C_PRODUCTO;

alter table PROXFAC
   drop foreign key FK_PROXFAC_FAC_PROXF_FACTURAS;

alter table PROXFAC
   drop foreign key FK_PROXFAC_PRD_DET_F_PRODUCTO;

alter table USUARIOS
   drop foreign key FK_USUARIOS_USUA_CLIE_CLIENTES;

drop table if exists BODEGAS;

drop table if exists CATEGORIA;

drop table if exists CLIENTES;


alter table COMPRAS
   drop foreign key FK_COMPRAS_PRV_COMPR_PROVEEDO;

drop table if exists COMPRAS;


alter table DETALLE_CARRITO
   drop foreign key FK_DETALLE__PRD_DET_C_PRODUCTO;

drop table if exists DETALLE_CARRITO;


alter table FACTURAS
   drop foreign key FK_FACTURAS_CLI_FACTU_CLIENTES;

drop table if exists FACTURAS;


alter table KARDEX
   drop foreign key FK_KARDEX_PRO_KARD_PRODUCTO;

alter table KARDEX
   drop foreign key FK_KARDEX_BOD_KARD_BODEGAS;

alter table KARDEX
   drop foreign key FK_KARDEX_FAC_KAR_FACTURAS;

alter table KARDEX
   drop foreign key FK_KARDEX_CMP_KAR_COMPRAS;

alter table KARDEX
   drop foreign key FK_KARDEX_TRN_KAR_TRANSACC;

drop table if exists KARDEX;


alter table PRODUCTOS
   drop foreign key FK_PRODUCTO_CAT_PROD_CATEGORI;

drop table if exists PRODUCTOS;

drop table if exists PROVEEDORES;


alter table PROXBOD
   drop foreign key FK_PROXBOD_PROD_DET__PRODUCTO;

alter table PROXBOD
   drop foreign key FK_PROXBOD_BOD_PROXB_BODEGAS;

drop table if exists PROXBOD;


alter table PROXCMP
   drop foreign key FK_PROXCMP_PRD_DET_C_PRODUCTO;

alter table PROXCMP
   drop foreign key FK_PROXCMP_CMP_PROXC_COMPRAS;

drop table if exists PROXCMP;


alter table PROXFAC
   drop foreign key FK_PROXFAC_PRD_DET_F_PRODUCTO;

alter table PROXFAC
   drop foreign key FK_PROXFAC_FAC_PROXF_FACTURAS;

drop table if exists PROXFAC;

drop table if exists TRANSACCION;


alter table USUARIOS
   drop foreign key FK_USUARIOS_USUA_CLIE_CLIENTES;

drop table if exists USUARIOS;

/*==============================================================*/
/* Table: BODEGAS                                               */
/*==============================================================*/
create table BODEGAS
(
   BOD_CODIGO           varchar(6) not null  comment '',
   BOD_DESCRIPCION      varchar(60) not null  comment '',
   BOD_DIRECCION        varchar(60) not null  comment '',
   BOD_NOMBRE_ENCARGADO varchar(60) not null  comment '',
   BOD_TELEFONO_ENCARGADO varchar(10) not null  comment '',
   primary key (BOD_CODIGO)
);

/*==============================================================*/
/* Table: CATEGORIA                                             */
/*==============================================================*/
create table CATEGORIA
(
   CAT_CODIGO           varchar(5) not null  comment '',
   CAT_NOMBRE           varchar(60) not null  comment '',
   CAT_DESCRIPCION      varchar(60) not null  comment '',
   primary key (CAT_CODIGO)
);

/*==============================================================*/
/* Table: CLIENTES                                              */
/*==============================================================*/
create table CLIENTES
(
   CLI_ID               int not null  comment '',
   CLI_CEDULA_RUC       varchar(13) not null  comment '',
   CLI_NOMBRE           varchar(60) not null  comment '',
   CLI_TELEFONO         varchar(10) not null  comment '',
   CLI_CORREO           varchar(60) not null  comment '',
   primary key (CLI_ID)
);

/*==============================================================*/
/* Table: COMPRAS                                               */
/*==============================================================*/
create table COMPRAS
(
   CMP_CODIGO           int not null  comment '',
   PRV_ID               int not null  comment '',
   CMP_FECHA_ENTREGA    date not null  comment '',
   CMP_ESTADO           varchar(10) not null  comment '',
   primary key (CMP_CODIGO)
);

/*==============================================================*/
/* Table: DETALLE_CARRITO                                       */
/*==============================================================*/
create table DETALLE_CARRITO
(
   PRD_CODIGO           varchar(8) not null  comment '',
   CAR_CODIGO           int not null  comment '',
   DET_CAR_CANTIDAD     int not null  comment '',
   primary key (PRD_CODIGO, CAR_CODIGO)
);

/*==============================================================*/
/* Table: FACTURAS                                              */
/*==============================================================*/
create table FACTURAS
(
   FAC_CODIGO           int not null  comment '',
   FAC_FECHA            datetime not null  comment '',
   FAC_SUBTOTAL         numeric(10,2) not null  comment '',
   FAC_IVA              numeric(10,2) not null  comment '',
   FAC_MONTO_TOTAL      numeric(10,2) not null  comment '',
   FAC_ESTADO           varchar(3) not null  comment '',
   ID_CARRITO           int not null  comment '',
   CLI_ID               int not null  comment '',
   primary key (FAC_CODIGO)
);

/*==============================================================*/
/* Table: KARDEX                                                */
/*==============================================================*/
create table KARDEX
(
   KAR_ID               int not null  comment '',
   PRD_CODIGO           varchar(8) not null  comment '',
   BOD_CODIGO           varchar(6) not null  comment '',
   FAC_CODIGO           int  comment '',
   CMP_CODIGO           int  comment '',
   TRN_ID               int  comment 'ATRIBUTO QUE ALMACENA EL ID DE UNA TRANSACCION (CLAVE PRIMARIA)',
   KAR_FECHA            datetime not null  comment '',
   KAR_SALDO            int not null  comment '',
   primary key (KAR_ID)
);

/*==============================================================*/
/* Table: PRODUCTOS                                             */
/*==============================================================*/
create table PRODUCTOS
(
   PRD_CODIGO           varchar(8) not null  comment '',
   CAT_CODIGO           varchar(5) not null  comment '',
   PRD_DESCRIPCION      varchar(60) not null  comment '',
   PRD_PRECIO           numeric(6,2) not null  comment '',
   PRD_COSTO_ADQUISICION numeric(6,2) not null  comment '',
   primary key (PRD_CODIGO)
);

/*==============================================================*/
/* Table: PROVEEDORES                                           */
/*==============================================================*/
create table PROVEEDORES
(
   PRV_ID               int not null  comment '',
   PRV_RUC              varchar(13) not null  comment '',
   PRV_RAZON_SOCIAL     varchar(60) not null  comment '',
   PRV_CORREO           varchar(60) not null  comment '',
   PRV_DIRECCION        varchar(60) not null  comment '',
   PRV_TELEFONO         varchar(10) not null  comment '',
   primary key (PRV_ID)
);

/*==============================================================*/
/* Table: PROXBOD                                               */
/*==============================================================*/
create table PROXBOD
(
   BOD_CODIGO           varchar(6) not null  comment '',
   PRD_CODIGO           varchar(8) not null  comment '',
   DET_BOD_CANTIDAD     int not null  comment '',
   DET_BOD_UBICACION    varchar(60) not null  comment '',
   primary key (BOD_CODIGO, PRD_CODIGO)
);

/*==============================================================*/
/* Table: PROXCMP                                               */
/*==============================================================*/
create table PROXCMP
(
   CMP_CODIGO           int not null  comment '',
   PRD_CODIGO           varchar(8) not null  comment '',
   DET_CMP_CANTIDAD     int not null  comment '',
   DET_CMP_COSTO_UNITARIO numeric(10,2) not null  comment '',
   ESTADO_PROXCMP       varchar(3) not null  comment '',
   primary key (CMP_CODIGO, PRD_CODIGO)
);

/*==============================================================*/
/* Table: PROXFAC                                               */
/*==============================================================*/
create table PROXFAC
(
   FAC_CODIGO           int not null  comment '',
   PRD_CODIGO           varchar(8) not null  comment '',
   DET_FAC_CANTIDAD     int not null  comment '',
   DET_FAC_PRECIO_UNITARIO numeric(10,2) not null  comment '',
   ESTADO_PROXFAC       varchar(3) not null  comment '',
   primary key (FAC_CODIGO, PRD_CODIGO)
);

/*==============================================================*/
/* Table: TRANSACCION                                           */
/*==============================================================*/
create table TRANSACCION
(
   TRN_ID               int not null  comment 'ATRIBUTO QUE ALMACENA EL ID DE UNA TRANSACCION (CLAVE PRIMARIA)',
   TRN_POS              int not null  comment '',
   TRN_NEG              int not null  comment '',
   primary key (TRN_ID)
);

/*==============================================================*/
/* Table: USUARIOS                                              */
/*==============================================================*/
create table USUARIOS
(
   USU_ID               int not null  comment '',
   CLI_ID               int not null  comment '',
   USU_NOMBRE           varchar(60) not null  comment '',
   USU_CONTRASENA       varchar(60) not null  comment '',
   primary key (USU_ID)
);

alter table COMPRAS add constraint FK_COMPRAS_PRV_COMPR_PROVEEDO foreign key (PRV_ID)
      references PROVEEDORES (PRV_ID) on delete restrict on update restrict;

alter table DETALLE_CARRITO add constraint FK_DETALLE__PRD_DET_C_PRODUCTO foreign key (PRD_CODIGO)
      references PRODUCTOS (PRD_CODIGO) on delete restrict on update restrict;

alter table FACTURAS add constraint FK_FACTURAS_CLI_FACTU_CLIENTES foreign key (CLI_ID)
      references CLIENTES (CLI_ID) on delete restrict on update restrict;

alter table KARDEX add constraint FK_KARDEX_BOD_KARD_BODEGAS foreign key (BOD_CODIGO)
      references BODEGAS (BOD_CODIGO) on delete restrict on update restrict;

alter table KARDEX add constraint FK_KARDEX_CMP_KAR_COMPRAS foreign key (CMP_CODIGO)
      references COMPRAS (CMP_CODIGO) on delete restrict on update restrict;

alter table KARDEX add constraint FK_KARDEX_FAC_KAR_FACTURAS foreign key (FAC_CODIGO)
      references FACTURAS (FAC_CODIGO) on delete restrict on update restrict;

alter table KARDEX add constraint FK_KARDEX_PRO_KARD_PRODUCTO foreign key (PRD_CODIGO)
      references PRODUCTOS (PRD_CODIGO) on delete restrict on update restrict;

alter table KARDEX add constraint FK_KARDEX_TRN_KAR_TRANSACC foreign key (TRN_ID)
      references TRANSACCION (TRN_ID) on delete restrict on update restrict;

alter table PRODUCTOS add constraint FK_PRODUCTO_CAT_PROD_CATEGORI foreign key (CAT_CODIGO)
      references CATEGORIA (CAT_CODIGO) on delete restrict on update restrict;

alter table PROXBOD add constraint FK_PROXBOD_BOD_PROXB_BODEGAS foreign key (BOD_CODIGO)
      references BODEGAS (BOD_CODIGO) on delete restrict on update restrict;

alter table PROXBOD add constraint FK_PROXBOD_PROD_DET__PRODUCTO foreign key (PRD_CODIGO)
      references PRODUCTOS (PRD_CODIGO) on delete restrict on update restrict;

alter table PROXCMP add constraint FK_PROXCMP_CMP_PROXC_COMPRAS foreign key (CMP_CODIGO)
      references COMPRAS (CMP_CODIGO) on delete restrict on update restrict;

alter table PROXCMP add constraint FK_PROXCMP_PRD_DET_C_PRODUCTO foreign key (PRD_CODIGO)
      references PRODUCTOS (PRD_CODIGO) on delete restrict on update restrict;

alter table PROXFAC add constraint FK_PROXFAC_FAC_PROXF_FACTURAS foreign key (FAC_CODIGO)
      references FACTURAS (FAC_CODIGO) on delete restrict on update restrict;

alter table PROXFAC add constraint FK_PROXFAC_PRD_DET_F_PRODUCTO foreign key (PRD_CODIGO)
      references PRODUCTOS (PRD_CODIGO) on delete restrict on update restrict;

alter table USUARIOS add constraint FK_USUARIOS_USUA_CLIE_CLIENTES foreign key (CLI_ID)
      references CLIENTES (CLI_ID) on delete restrict on update restrict;
