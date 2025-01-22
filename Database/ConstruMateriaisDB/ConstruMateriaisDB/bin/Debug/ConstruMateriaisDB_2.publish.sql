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
PRINT N'Criando Tabela [dbo].[Produtos]...';


GO
CREATE TABLE [dbo].[Produtos] (
    [ProdutoID]             INT             IDENTITY (1, 1) NOT NULL,
    [CategoriaID]           INT             NULL,
    [Nome]                  VARCHAR (100)   NOT NULL,
    [Descricao]             VARCHAR (500)   NULL,
    [PrecoUnitario]         DECIMAL (10, 2) NOT NULL,
    [QuantidadeEstoque]     INT             NOT NULL,
    [DataUltimaAtualizacao] DATETIME        NULL,
    [Foto]                  VARBINARY (MAX) NOT NULL,
    [Foto2]                 VARBINARY (MAX) NULL,
    [Foto3]                 VARBINARY (MAX) NULL,
    PRIMARY KEY CLUSTERED ([ProdutoID] ASC)
);


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[Produtos]...';


GO
ALTER TABLE [dbo].[Produtos]
    ADD DEFAULT GETDATE() FOR [DataUltimaAtualizacao];


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[Produtos]...';


GO
ALTER TABLE [dbo].[Produtos] WITH NOCHECK
    ADD FOREIGN KEY ([CategoriaID]) REFERENCES [dbo].[Categorias] ([CategoriaID]);


GO
