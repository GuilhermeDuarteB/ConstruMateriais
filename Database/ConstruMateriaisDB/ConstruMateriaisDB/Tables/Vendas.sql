CREATE TABLE [dbo].Vendas (
    VendaID INT PRIMARY KEY IDENTITY(1,1),
    ClienteID INT FOREIGN KEY REFERENCES Clientes(IdCliente),
    DataVenda DATETIME DEFAULT GETDATE(),
    ValorTotal DECIMAL(10,2) NOT NULL,
    FormaPagamento VARCHAR(50),
    [Status] VARCHAR(20)
);
