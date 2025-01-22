CREATE TABLE [dbo].ItensVenda (
    ItemVendaID INT PRIMARY KEY IDENTITY(1,1),
    VendaID INT FOREIGN KEY REFERENCES Vendas(VendaID),
    ProdutoID INT FOREIGN KEY REFERENCES Produtos(ProdutoID),
    Quantidade INT NOT NULL,
    PrecoUnitario DECIMAL(10,2) NOT NULL
);