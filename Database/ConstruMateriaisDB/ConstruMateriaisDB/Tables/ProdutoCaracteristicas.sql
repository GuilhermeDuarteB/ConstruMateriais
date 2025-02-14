CREATE TABLE [dbo].[ProdutoCaracteristicas] (
    ProdutoCaracteristicaID INT PRIMARY KEY IDENTITY(1,1),
    ProdutoID INT FOREIGN KEY REFERENCES Produtos(ProdutoID),
    CaracteristicaID INT FOREIGN KEY REFERENCES Caracteristicas(CaracteristicaID),
    ValorID INT FOREIGN KEY REFERENCES CaracteristicaValores(ValorID),
    DataCriacao DATETIME DEFAULT GETDATE()
);