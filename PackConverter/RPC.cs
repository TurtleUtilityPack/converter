using DiscordRPC;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using Button = DiscordRPC.Button;

namespace TurtleRPC
{
    public class Mods
    {
        public static DiscordRpcClient Client;
        public static bool doDiscord = true;

        public static void StartRPC()
        {
            Client = new DiscordRpcClient("856229870035140609");
            Client.Initialize();
            if (doDiscord)
                Client.SetPresence(new RichPresence()
                {
                    Timestamps = Timestamps.Now,
                    Details = "In home",
                    Assets = new Assets() { LargeImageKey = "ico"},
                    Buttons = new Button[] { new Button { Label = "Turtle Discord", Url = "https://discord.gg/turtleclient" }, }
                });
        }

        public static void Dispose()
        {
            Client.Dispose();
        }

        public static bool canDispose
        {
            get
            {
                if (Client.ApplicationID != "")
                    return true;
                return false;
            }
        }
    }
}