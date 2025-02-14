CREATE TABLE [dbo].[Caracteristicas] (
    CaracteristicaID INT PRIMARY KEY IDENTITY(1,1),
    Nome NVARCHAR(100) NOT NULL,
    Descricao NVARCHAR(255),
    DataCriacao DATETIME DEFAULT GETDATE()
);