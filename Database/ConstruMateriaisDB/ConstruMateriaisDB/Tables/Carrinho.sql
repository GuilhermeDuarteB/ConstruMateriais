CREATE TABLE [dbo].Carrinho (
    CarrinhoID INT PRIMARY KEY IDENTITY(1,1),
    ClienteID INT FOREIGN KEY REFERENCES Clientes(IdCliente),
    ProdutoID INT FOREIGN KEY REFERENCES Produtos(ProdutoID),
    Quantidade INT NOT NULL,
    DataAdicao DATETIME DEFAULT GETDATE(),
    CONSTRAINT UC_ClienteProduto UNIQUE (ClienteID, ProdutoID)
);