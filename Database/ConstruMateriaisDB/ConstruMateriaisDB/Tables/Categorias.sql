CREATE TABLE [dbo].Categorias (
    CategoriaID INT PRIMARY KEY IDENTITY(1,1),
    Nome VARCHAR(50) NOT NULL,
    Descricao VARCHAR(200)
);