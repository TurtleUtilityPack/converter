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
using DiscordRPC;
using TurtleRPC;
using Button = DiscordRPC.Button;

namespace PackConverter
{
    public partial class UI : Form
    {
        public UI()
        {
            InitializeComponent();
        }

        private void homebtn_Click(object sender, EventArgs e)
        {
            homepnl.Visible = true;

            homebtn.Checked = true;
            changelogbtn.Checked = false;
            settingsbtn.Checked = false;

            Mods.Client.SetPresence(new RichPresence()
            {
                Timestamps = Timestamps.Now,
                Details = "In home",
                Assets = new Assets() { LargeImageKey = "ico" },
                Buttons = new Button[] { new Button { Label = "Turtle Discord", Url = "https://discord.gg/turtleclient" }, }
            });

        }

        private void changelogbtn_Click(object sender, EventArgs e)
        {
            changelogbtn.Checked = true;
            homebtn.Checked = false;
            settingsbtn.Checked = false;

            Mods.Client.SetPresence(new RichPresence()
            {
                Timestamps = Timestamps.Now,
                Details = "Looking at changelogs",
                Assets = new Assets() { LargeImageKey = "ico" },
                Buttons = new Button[] { new Button { Label = "Turtle Discord", Url = "https://discord.gg/turtleclient" }, }
            });

        }

        private void settingsbtn_Click(object sender, EventArgs e)
        {
            settingsbtn.Checked = true;
            homebtn.Checked = false;
            changelogbtn.Checked = false;

            Mods.Client.SetPresence(new RichPresence()
            {
                Timestamps = Timestamps.Now,
                Details = "Configuring settings",
                Assets = new Assets() { LargeImageKey = "ico" },
                Buttons = new Button[] { new Button { Label = "Turtle Discord", Url = "https://discord.gg/turtleclient" }, }
            });

        }

        private void UI_Load(object sender, EventArgs e)
        {
            Mods.StartRPC();
        }

        private void closebtn_Click(object sender, EventArgs e)
        {
            this.Close();
        }

        private void minibtn_Click(object sender, EventArgs e)
        {
            WindowState = FormWindowState.Minimized;
        }

        private void launchMCbtn_Click(object sender, EventArgs e)
        {
            Process.Start("Minecraft://");
        }
    }
}
