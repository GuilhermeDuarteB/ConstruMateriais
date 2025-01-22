/*
Script de implantação para db-construmateriais

Este código foi gerado por uma ferramenta.
As alterações feitas nesse arquivo poderão causar comportamento incorreto e serão perdidas se
o código for gerado novamente.
*/

GO
SET ANSI_NULLS, ANSI_PADDING, ANSI_WARNINGS, ARITHABORT, CONCAT_NULL_YIELDS_NULL, QUOTED_IDENTIFIER ON;

SET NUMERIC_ROUNDABORT OFF;


GO
:setvar DatabaseName "db-construmateriais"
:setvar DefaultFilePrefix "db-construmateriais"
:setvar DefaultDataPath ""
:setvar DefaultLogPath ""

GO
:on error exit
GO
/*
Detecta o modo SQLCMD e desabilita a execução do script se o modo SQLCMD não tiver suporte.
Para reabilitar o script após habilitar o modo SQLCMD, execute o comando a seguir:
SET NOEXEC OFF; 
*/
:setvar __IsSqlCmdEnabled "True"
GO
IF N'$(__IsSqlCmdEnabled)' NOT LIKE N'True'
    BEGIN
        PRINT N'O modo SQLCMD deve ser habilitado para executar esse script com êxito.';
        SET NOEXEC ON;
    END


GO
IF EXISTS (SELECT 1
           FROM   [sys].[databases]
           WHERE  [name] = N'$(DatabaseName)')
    BEGIN
        ALTER DATABASE [$(DatabaseName)]
            SET QUERY_STORE = OFF 
            WITH ROLLBACK IMMEDIATE;
    END


GO
PRINT N'Criando Tabela [dbo].[Categorias]...';


GO
CREATE TABLE [dbo].[Categorias] (
    [CategoriaID] INT           IDENTITY (1, 1) NOT NULL,
    [Nome]        VARCHAR (50)  NOT NULL,
    [Descricao]   VARCHAR (200) NULL,
    PRIMARY KEY CLUSTERED ([CategoriaID] ASC)
);


GO
PRINT N'Criando Tabela [dbo].[Clientes]...';


GO
CREATE TABLE [dbo].[Clientes] (
    [IdCliente]      INT         IDENTITY (1, 1) NOT NULL,
    [Nome]           NCHAR (50)  NOT NULL,
    [NomeUtilizador] NCHAR (30)  NOT NULL,
    [Password]       NCHAR (255) NOT NULL,
    [Email]          NCHAR (255) NOT NULL,
    [Telefone]       NCHAR (9)   NOT NULL,
    [Morada]         NCHAR (255) NOT NULL,
    [Contribuinte]   NCHAR (9)   NULL,
    PRIMARY KEY CLUSTERED ([IdCliente] ASC)
);


GO
PRINT N'Criando Tabela [dbo].[Produtos]...';


GO
CREATE TABLE [dbo].[Produtos] (
    [ProdutoID]             INT             IDENTITY (1, 1) NOT NULL,
    [CategoriaID]           INT             NULL,
    [Nome]                  VARCHAR (100)   NOT NULL,
    [Descricao]             VARCHAR (500)   NULL,
    [PrecoUnitario]         DECIMAL (10, 2) NOT NULL,
    [QuantidadeEstoque]     INT             NOT NULL,
    [EstoqueMinimo]         INT             NOT NULL,
    [UnidadeMedida]         VARCHAR (20)    NULL,
    [Codigo]                VARCHAR (20)    NULL,
    [DataUltimaAtualizacao] DATETIME        NULL,
    PRIMARY KEY CLUSTERED ([ProdutoID] ASC),
    UNIQUE NONCLUSTERED ([Codigo] ASC)
);


GO
PRINT N'Criando Tabela [dbo].[Vendas]...';


GO
CREATE TABLE [dbo].[Vendas] (
    [VendaID]        INT             IDENTITY (1, 1) NOT NULL,
    [ClienteID]      INT             NULL,
    [DataVenda]      DATETIME        NULL,
    [ValorTotal]     DECIMAL (10, 2) NOT NULL,
    [FormaPagamento] VARCHAR (50)    NULL,
    [Status]         VARCHAR (20)    NULL,
    PRIMARY KEY CLUSTERED ([VendaID] ASC)
);


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[Produtos]...';


GO
ALTER TABLE [dbo].[Produtos]
    ADD DEFAULT GETDATE() FOR [DataUltimaAtualizacao];


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[Vendas]...';


GO
ALTER TABLE [dbo].[Vendas]
    ADD DEFAULT GETDATE() FOR [DataVenda];


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[Produtos]...';


GO
ALTER TABLE [dbo].[Produtos] WITH NOCHECK
    ADD FOREIGN KEY ([CategoriaID]) REFERENCES [dbo].[Categorias] ([CategoriaID]);


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[Vendas]...';


GO
ALTER TABLE [dbo].[Vendas] WITH NOCHECK
    ADD FOREIGN KEY ([ClienteID]) REFERENCES [dbo].[Clientes] ([IdCliente]);


GO
