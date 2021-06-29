namespace PackConverter;

    public partial class Config
{
    public string from;
    public string to;
    public string javaVersion;
    public string zipPath;

    public Config (string from, string to, string javaVersion, string zipPath)

        {

        this.from = from;
        this.to = to;
        this.javaVersion = javaVersion;
        this.zipPath = zipPath;

        }

    public compress()
    {
        json e = json.encode(this);
        return e;
    }
}