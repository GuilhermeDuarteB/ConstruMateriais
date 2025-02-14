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
PRINT N'Criando Tabela [dbo].[Caracteristicas]...';


GO
CREATE TABLE [dbo].[Caracteristicas] (
    [CaracteristicaID] INT           IDENTITY (1, 1) NOT NULL,
    [Nome]             VARCHAR (100) NOT NULL,
    [Descricao]        VARCHAR (255) NULL,
    [DataCriacao]      DATETIME      NULL,
    PRIMARY KEY CLUSTERED ([CaracteristicaID] ASC)
);


GO
PRINT N'Criando Tabela [dbo].[ProdutoCaracteristicas]...';


GO
CREATE TABLE [dbo].[ProdutoCaracteristicas] (
    [ProdutoCaracteristicaID] INT           IDENTITY (1, 1) NOT NULL,
    [ProdutoID]               INT           NULL,
    [CaracteristicaID]        INT           NULL,
    [Valor]                   VARCHAR (255) NULL,
    [DataCriacao]             DATETIME      NULL,
    PRIMARY KEY CLUSTERED ([ProdutoCaracteristicaID] ASC)
);


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[Caracteristicas]...';


GO
ALTER TABLE [dbo].[Caracteristicas]
    ADD DEFAULT GETDATE() FOR [DataCriacao];


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[ProdutoCaracteristicas]...';


GO
ALTER TABLE [dbo].[ProdutoCaracteristicas]
    ADD DEFAULT GETDATE() FOR [DataCriacao];


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[ProdutoCaracteristicas]...';


GO
ALTER TABLE [dbo].[ProdutoCaracteristicas] WITH NOCHECK
    ADD FOREIGN KEY ([ProdutoID]) REFERENCES [dbo].[Produtos] ([ProdutoID]);


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[ProdutoCaracteristicas]...';


GO
ALTER TABLE [dbo].[ProdutoCaracteristicas] WITH NOCHECK
    ADD FOREIGN KEY ([CaracteristicaID]) REFERENCES [dbo].[Caracteristicas] ([CaracteristicaID]);


GO
