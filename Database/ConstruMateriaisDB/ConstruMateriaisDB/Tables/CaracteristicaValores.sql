CREATE TABLE [dbo].[CaracteristicaValores] (
    ValorID INT PRIMARY KEY IDENTITY(1,1),
    CaracteristicaID INT FOREIGN KEY REFERENCES Caracteristicas(CaracteristicaID),
    Valor NVARCHAR(255) NOT NULL,
    Descricao NVARCHAR(255),
    DataCriacao DATETIME DEFAULT GETDATE()
);
