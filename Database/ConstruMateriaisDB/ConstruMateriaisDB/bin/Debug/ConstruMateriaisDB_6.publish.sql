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
PRINT N'Criando Tabela [dbo].[ItensVenda]...';


GO
CREATE TABLE [dbo].[ItensVenda] (
    [ItemVendaID]   INT             IDENTITY (1, 1) NOT NULL,
    [VendaID]       INT             NULL,
    [ProdutoID]     INT             NULL,
    [Quantidade]    INT             NOT NULL,
    [PrecoUnitario] DECIMAL (10, 2) NOT NULL,
    PRIMARY KEY CLUSTERED ([ItemVendaID] ASC)
);


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[ItensVenda]...';


GO
ALTER TABLE [dbo].[ItensVenda] WITH NOCHECK
    ADD FOREIGN KEY ([VendaID]) REFERENCES [dbo].[Vendas] ([VendaID]);


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[ItensVenda]...';


GO
ALTER TABLE [dbo].[ItensVenda] WITH NOCHECK
    ADD FOREIGN KEY ([ProdutoID]) REFERENCES [dbo].[Produtos] ([ProdutoID]);


GO
