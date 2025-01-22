CREATE TABLE [dbo].[EnderecosCliente] (
    [EnderecoID] INT IDENTITY(1,1) PRIMARY KEY,
    [ClienteID] INT FOREIGN KEY REFERENCES Clientes(IdCliente),
    [Morada] NVARCHAR(255) NOT NULL,
    [CodigoPostal] NVARCHAR(8) NOT NULL,
    [Localidade] NVARCHAR(100) NOT NULL,
    [Complemento] NVARCHAR(100) NULL,
    [NumeroPorta] NVARCHAR(10) NULL,
    [Principal] BIT DEFAULT 0,
    [DataCriacao] DATETIME DEFAULT GETDATE()
);