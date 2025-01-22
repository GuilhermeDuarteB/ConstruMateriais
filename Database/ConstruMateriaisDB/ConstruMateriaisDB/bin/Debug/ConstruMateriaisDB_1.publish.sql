/*
Script de implantação para db-construmateriais

Este código foi gerado por uma ferramenta.
As alterações feitas nesse arquivo poderão causar comportamento incorreto e serão perdidas se
o código for gerado novamente.
*/

GO
SET ANSI_NULLS, ANSI_PADDING, ANSI_WARNINGS, ARITHABORT, CONCAT_NULL_YIELDS_NULL, QUOTED_IDENTIFIER ON;

SET NUMERIC_ROUNDABORT OFF;


GO
:setvar DatabaseName "db-construmateriais"
:setvar DefaultFilePrefix "db-construmateriais"
:setvar DefaultDataPath ""
:setvar DefaultLogPath ""

GO
:on error exit
GO
/*
Detecta o modo SQLCMD e desabilita a execução do script se o modo SQLCMD não tiver suporte.
Para reabilitar o script após habilitar o modo SQLCMD, execute o comando a seguir:
SET NOEXEC OFF; 
*/
:setvar __IsSqlCmdEnabled "True"
GO
IF N'$(__IsSqlCmdEnabled)' NOT LIKE N'True'
    BEGIN
        PRINT N'O modo SQLCMD deve ser habilitado para executar esse script com êxito.';
        SET NOEXEC ON;
    END


GO
IF EXISTS (SELECT 1
           FROM   [sys].[databases]
           WHERE  [name] = N'$(DatabaseName)')
    BEGIN
        ALTER DATABASE [$(DatabaseName)]
            SET QUERY_STORE = OFF 
            WITH ROLLBACK IMMEDIATE;
    END


GO
/*
A coluna [dbo].[Produtos].[Foto] na tabela [dbo].[Produtos] deve ser adicionada, mas a coluna não possui valor padrão e não permite valores NULL. Se a tabela contiver dados, o script ALTER não funcionará. Para evitar o problema, você deve: adicionar um valor padrão para a coluna, marcá-la para permitir valores NULL ou habilitar a geração de padrões inteligentes como uma opção de implantação.
*/

IF EXISTS (select top 1 1 from [dbo].[Produtos])
    RAISERROR (N'Linhas foram detectadas. A atualização de esquema está sendo encerrada porque pode ocorrer perda de dados.', 16, 127) WITH NOWAIT

GO
PRINT N'Alterando Tabela [dbo].[Produtos]...';


GO
ALTER TABLE [dbo].[Produtos]
    ADD [Foto]  VARBINARY (MAX) NOT NULL,
        [Foto2] VARBINARY (MAX) NULL,
        [Foto3] VARBINARY (MAX) NULL;


GO
PRINT N'Atualização concluída.';


GO
