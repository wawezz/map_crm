module.exports = {
    resolve: {
        extensions: [".js", ".json", ".vue", ".ts"],
        root: path.resolve(__dirname, 'src/frontend'),
        alias: {
            "~": path.resolve(__dirname, "src/frontend/source"),
        }
    },
};
