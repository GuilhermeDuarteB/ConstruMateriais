CREATE TABLE [dbo].Produtos (
    ProdutoID INT PRIMARY KEY IDENTITY(1,1),
    CategoriaID INT FOREIGN KEY REFERENCES Categorias(CategoriaID),
    Nome VARCHAR(100) NOT NULL,
    Descricao VARCHAR(500),
    PrecoUnitario DECIMAL(10,2) NOT NULL,
    QuantidadeEstoque INT NOT NULL,
    DataCriacao DATETIME DEFAULT GETDATE(),
    DataModificacao DATETIME DEFAULT GETDATE(),
    Status BIT DEFAULT 1,
    [Foto] VARBINARY(MAX) NOT NULL,
    [Foto2] VARBINARY(MAX) NULL,
    [Foto3] VARBINARY(MAX) NULL
);
