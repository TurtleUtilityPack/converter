using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.IO.Compression;
using System.Linq;
using System.Net;
using System.Runtime.InteropServices;
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

        public string From;
        public string To;
        public string javaVersion;
        public string zipPath;

        public UI()
        {
            WebClient Converter = new WebClient();
            InitializeComponent();
            Directory.CreateDirectory(@"c:\Turtle");
            Directory.CreateDirectory(@"c:\Turtle/Converted_Packs");
            Directory.CreateDirectory(@"c:\Turtle/Converter");

            //Converter.DownloadFile("https://github.com/ZKiev/TurtleFiles/blob/master/Converter.php", @"C:\Turtle/Converter/Converter.php");
        }

        private void homebtn_Click(object sender, EventArgs e)
        {
          
            settingspnl.Visible = false;

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
           
            settingspnl.Visible = true;

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
            Process.Start(@"C:\Turtle/Converted_Packs/");
        }

        private void convertbtn_Click(object sender, EventArgs e)
        {
            Process.Start("");
        }

        private void guna2HtmlLabel1_Click(object sender, EventArgs e)
        {

        }

        private void from_SelectedIndexChanged(object sender, EventArgs e)
        {

        }

        private void guna2Button1_Click(object sender, EventArgs e)
        {
            this.From = from.SelectedText;
        }
    }
}
