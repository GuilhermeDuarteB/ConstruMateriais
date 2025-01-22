CREATE TABLE [dbo].[Clientes]
(
    [IdCliente] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [Nome] NCHAR(50) NOT NULL,
    [NomeUtilizador] NCHAR(30) NOT NULL,
    [Password] NCHAR(255) NOT NULL,
    [Email] NCHAR(255) NOT NULL,
    [Telefone] NCHAR(9) NOT NULL,
    [Morada] NVARCHAR(255) NULL,
    [CodigoPostal] NVARCHAR(8) NULL DEFAULT '0000-000',
    [Localidade] NVARCHAR(100) NULL DEFAULT '',
    [Complemento] NVARCHAR(100) NULL,
    [NumeroPorta] NVARCHAR(10) NULL,
    [Contribuinte] NCHAR(9) NULL
);