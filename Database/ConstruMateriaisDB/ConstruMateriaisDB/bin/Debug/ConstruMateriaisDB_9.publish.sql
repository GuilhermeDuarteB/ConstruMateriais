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
/*
A coluna Nome na tabela [dbo].[Caracteristicas] deve ser alterada de NULL para NOT NULL. Se a tabela contiver dados, o script ALTER talvez não funcione. Para evitar o problema, você deve adicionar valores a essa coluna para todas as linhas, marcá-la para permitir valores NULL ou habilitar a geração de padrões inteligentes como uma opção de implantação.
*/

IF EXISTS (select top 1 1 from [dbo].[Caracteristicas])
    RAISERROR (N'Linhas foram detectadas. A atualização de esquema está sendo encerrada porque pode ocorrer perda de dados.', 16, 127) WITH NOWAIT

GO
/*
Ignorando a coluna [dbo].[ProdutoCaracteristicas].[Valor]; poderá ocorrer perda de dados.
*/

IF EXISTS (select top 1 1 from [dbo].[ProdutoCaracteristicas])
    RAISERROR (N'Linhas foram detectadas. A atualização de esquema está sendo encerrada porque pode ocorrer perda de dados.', 16, 127) WITH NOWAIT

GO
PRINT N'Removendo Restrição Padrão restrição sem nome em [dbo].[ProdutoCaracteristicas]...';


GO
ALTER TABLE [dbo].[ProdutoCaracteristicas] DROP CONSTRAINT [DF__ProdutoCa__DataC__1E6F845E];


GO
PRINT N'Removendo Chave Estrangeira restrição sem nome em [dbo].[ProdutoCaracteristicas]...';


GO
ALTER TABLE [dbo].[ProdutoCaracteristicas] DROP CONSTRAINT [FK__ProdutoCa__Produ__1F63A897];


GO
PRINT N'Removendo Chave Estrangeira restrição sem nome em [dbo].[ProdutoCaracteristicas]...';


GO
ALTER TABLE [dbo].[ProdutoCaracteristicas] DROP CONSTRAINT [FK__ProdutoCa__Carac__2057CCD0];


GO
PRINT N'Alterando Tabela [dbo].[Caracteristicas]...';


GO
ALTER TABLE [dbo].[Caracteristicas] ALTER COLUMN [Nome] NVARCHAR (100) NOT NULL;


GO
PRINT N'Iniciando a recompilação da tabela [dbo].[ProdutoCaracteristicas]...';


GO
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;

SET XACT_ABORT ON;

CREATE TABLE [dbo].[tmp_ms_xx_ProdutoCaracteristicas] (
    [ProdutoCaracteristicaID] INT      IDENTITY (1, 1) NOT NULL,
    [ProdutoID]               INT      NULL,
    [CaracteristicaID]        INT      NULL,
    [ValorID]                 INT      NULL,
    [DataCriacao]             DATETIME DEFAULT GETDATE() NULL,
    PRIMARY KEY CLUSTERED ([ProdutoCaracteristicaID] ASC)
);

IF EXISTS (SELECT TOP 1 1 
           FROM   [dbo].[ProdutoCaracteristicas])
    BEGIN
        SET IDENTITY_INSERT [dbo].[tmp_ms_xx_ProdutoCaracteristicas] ON;
        INSERT INTO [dbo].[tmp_ms_xx_ProdutoCaracteristicas] ([ProdutoCaracteristicaID], [ProdutoID], [CaracteristicaID], [DataCriacao])
        SELECT   [ProdutoCaracteristicaID],
                 [ProdutoID],
                 [CaracteristicaID],
                 [DataCriacao]
        FROM     [dbo].[ProdutoCaracteristicas]
        ORDER BY [ProdutoCaracteristicaID] ASC;
        SET IDENTITY_INSERT [dbo].[tmp_ms_xx_ProdutoCaracteristicas] OFF;
    END

DROP TABLE [dbo].[ProdutoCaracteristicas];

EXECUTE sp_rename N'[dbo].[tmp_ms_xx_ProdutoCaracteristicas]', N'ProdutoCaracteristicas';

COMMIT TRANSACTION;

SET TRANSACTION ISOLATION LEVEL READ COMMITTED;


GO
PRINT N'Alterando Tabela [dbo].[Produtos]...';


GO
ALTER TABLE [dbo].[Produtos] ALTER COLUMN [Descricao] NVARCHAR (500) NULL;

ALTER TABLE [dbo].[Produtos] ALTER COLUMN [Nome] NVARCHAR (100) NOT NULL;


GO
PRINT N'Criando Tabela [dbo].[CaracteristicaValores]...';


GO
CREATE TABLE [dbo].[CaracteristicaValores] (
    [ValorID]          INT            IDENTITY (1, 1) NOT NULL,
    [CaracteristicaID] INT            NULL,
    [Valor]            NVARCHAR (255) NOT NULL,
    [Descricao]        NVARCHAR (255) NULL,
    [DataCriacao]      DATETIME       NULL,
    PRIMARY KEY CLUSTERED ([ValorID] ASC)
);


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[CaracteristicaValores]...';


GO
ALTER TABLE [dbo].[CaracteristicaValores]
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
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[ProdutoCaracteristicas]...';


GO
ALTER TABLE [dbo].[ProdutoCaracteristicas] WITH NOCHECK
    ADD FOREIGN KEY ([ValorID]) REFERENCES [dbo].[CaracteristicaValores] ([ValorID]);


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[CaracteristicaValores]...';


GO
ALTER TABLE [dbo].[CaracteristicaValores] WITH NOCHECK
    ADD FOREIGN KEY ([CaracteristicaID]) REFERENCES [dbo].[Caracteristicas] ([CaracteristicaID]);


GO
