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
PRINT N'Removendo Chave Estrangeira restrição sem nome em [dbo].[Vendas]...';


GO
ALTER TABLE [dbo].[Vendas] DROP CONSTRAINT [FK__Vendas__ClienteI__6BE40491];


GO
PRINT N'Removendo Chave Estrangeira restrição sem nome em [dbo].[Carrinho]...';


GO
ALTER TABLE [dbo].[Carrinho] DROP CONSTRAINT [FK__Carrinho__Client__7A3223E8];


GO
PRINT N'Iniciando a recompilação da tabela [dbo].[Clientes]...';


GO
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE;

SET XACT_ABORT ON;

CREATE TABLE [dbo].[tmp_ms_xx_Clientes] (
    [IdCliente]      INT            IDENTITY (1, 1) NOT NULL,
    [Nome]           NCHAR (50)     NOT NULL,
    [NomeUtilizador] NCHAR (30)     NOT NULL,
    [Password]       NCHAR (255)    NOT NULL,
    [Email]          NCHAR (255)    NOT NULL,
    [Telefone]       NCHAR (9)      NOT NULL,
    [Morada]         NVARCHAR (255) NULL,
    [CodigoPostal]   NVARCHAR (8)   DEFAULT '0000-000' NULL,
    [Localidade]     NVARCHAR (100) DEFAULT '' NULL,
    [Complemento]    NVARCHAR (100) NULL,
    [NumeroPorta]    NVARCHAR (10)  NULL,
    [Contribuinte]   NCHAR (9)      NULL,
    PRIMARY KEY CLUSTERED ([IdCliente] ASC)
);

IF EXISTS (SELECT TOP 1 1 
           FROM   [dbo].[Clientes])
    BEGIN
        SET IDENTITY_INSERT [dbo].[tmp_ms_xx_Clientes] ON;
        INSERT INTO [dbo].[tmp_ms_xx_Clientes] ([IdCliente], [Nome], [NomeUtilizador], [Password], [Email], [Telefone], [Morada], [Contribuinte])
        SELECT   [IdCliente],
                 [Nome],
                 [NomeUtilizador],
                 [Password],
                 [Email],
                 [Telefone],
                 [Morada],
                 [Contribuinte]
        FROM     [dbo].[Clientes]
        ORDER BY [IdCliente] ASC;
        SET IDENTITY_INSERT [dbo].[tmp_ms_xx_Clientes] OFF;
    END

DROP TABLE [dbo].[Clientes];

EXECUTE sp_rename N'[dbo].[tmp_ms_xx_Clientes]', N'Clientes';

COMMIT TRANSACTION;

SET TRANSACTION ISOLATION LEVEL READ COMMITTED;


GO
PRINT N'Criando Tabela [dbo].[EnderecosCliente]...';


GO
CREATE TABLE [dbo].[EnderecosCliente] (
    [EnderecoID]   INT            IDENTITY (1, 1) NOT NULL,
    [ClienteID]    INT            NULL,
    [Morada]       NVARCHAR (255) NOT NULL,
    [CodigoPostal] NVARCHAR (8)   NOT NULL,
    [Localidade]   NVARCHAR (100) NOT NULL,
    [Complemento]  NVARCHAR (100) NULL,
    [NumeroPorta]  NVARCHAR (10)  NULL,
    [Principal]    BIT            NULL,
    [DataCriacao]  DATETIME       NULL,
    PRIMARY KEY CLUSTERED ([EnderecoID] ASC)
);


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[EnderecosCliente]...';


GO
ALTER TABLE [dbo].[EnderecosCliente]
    ADD DEFAULT 0 FOR [Principal];


GO
PRINT N'Criando Restrição Padrão restrição sem nome em [dbo].[EnderecosCliente]...';


GO
ALTER TABLE [dbo].[EnderecosCliente]
    ADD DEFAULT GETDATE() FOR [DataCriacao];


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[Vendas]...';


GO
ALTER TABLE [dbo].[Vendas] WITH NOCHECK
    ADD FOREIGN KEY ([ClienteID]) REFERENCES [dbo].[Clientes] ([IdCliente]);


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[Carrinho]...';


GO
ALTER TABLE [dbo].[Carrinho] WITH NOCHECK
    ADD FOREIGN KEY ([ClienteID]) REFERENCES [dbo].[Clientes] ([IdCliente]);


GO
PRINT N'Criando Chave Estrangeira restrição sem nome em [dbo].[EnderecosCliente]...';


GO
ALTER TABLE [dbo].[EnderecosCliente] WITH NOCHECK
    ADD FOREIGN KEY ([ClienteID]) REFERENCES [dbo].[Clientes] ([IdCliente]);


GO
