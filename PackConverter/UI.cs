﻿using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.IO.Compression;
using System.Linq;
using System.Net;
using System.Net.Mail;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using System.Security.Cryptography.X509Certificates;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using DiscordRPC;
using DiscordRPC.Helper;
using Newtonsoft.Json;
using TurtleRPC;
using Button = DiscordRPC.Button;
using PackConverter;

namespace PackConverter
{
    public partial class UI : Form
    {

        public string From1;
        public string To;
        public string JavaVersion;
        public OpenFileDialog zipPath;
        public object instance;

        public Config config;

            public UI()
        {
            WebClient Converter = new WebClient();
            InitializeComponent();
            Directory.CreateDirectory(@"c:\Turtle");
            Directory.CreateDirectory(@"c:\Turtle/Converted_Packs");
            Directory.CreateDirectory(@"c:\Turtle/Converter");

            this.instance = this;



            //Converter.DownloadFile("https://github.com/ZKiev/TurtleFiles/blob/master/Converter.php", @"C:\Turtle/Converter/Converter.php");
        }

        public object getInstance()
        {
            return this.instance;
        }

        private void homebtn_Click(object sender, EventArgs e)
        {

            settingspnl.Visible = false;
            homepnl.Visible = true;

            homebtn.Checked = true;
            changelogbtn.Checked = false;
            settingsbtn.Checked = false;


            if(zipPath != null & From1 != null & JavaVersion != null)
            {
                homepnltxt.Text = "Your current settings are set up.";
            }

            Mods.Client.SetPresence(new RichPresence()
            {
                Timestamps = Timestamps.Now,
                Details = "In home",
                Assets = new Assets() {LargeImageKey = "ico"},
                Buttons = new Button[] {new Button {Label = "Turtle Discord", Url = "https://discord.gg/turtleclient"},}
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
                Assets = new Assets() {LargeImageKey = "ico"},
                Buttons = new Button[] {new Button {Label = "Turtle Discord", Url = "https://discord.gg/turtleclient"},}
            });

        }

        private void settingsbtn_Click(object sender, EventArgs e)
        {

            settingspnl.Visible = true;
            homepnl.Visible = false;

            settingsbtn.Checked = true;
            homebtn.Checked = false;
            changelogbtn.Checked = false;

            Mods.Client.SetPresence(new RichPresence()
            {
                Timestamps = Timestamps.Now,
                Details = "Configuring settings",
                Assets = new Assets() {LargeImageKey = "ico"},
                Buttons = new Button[] {new Button {Label = "Turtle Discord", Url = "https://discord.gg/turtleclient"},}
            });

        }

        private void UI_Load(object sender, EventArgs e)
        {
            Mods.StartRPC();
                        
            if (File.Exists("config.json"))
            {
                StreamReader sr = new StreamReader("config.json");
                string config = sr.ReadToEndAsync().Result;
                Config h = JsonConvert.DeserializeObject<Config>(config);
                this.from1.Text = h.from;
                this.javaVersion.Text = h.javaVersion;
            }
        }

        private void closebtn_Click(object sender, EventArgs e)
        {
            this.Close();
            Mods.Client.Dispose();
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
            From1 = from1.SelectedItem.ToString();

            string to;

            switch (From1)
            {
                case "Bedrock":
                    to = "Java";
                    break;
                case "Java":
                    to = "Bedrock";
                    break;

                default:
                    to = "Bedrock";
                    break;

            }
            
            if (zipPath == null || zipPath.FileName.ToString() == null)
            {
                MessageBox.Show("Error", "You didn't choose which file to convert!", MessageBoxButtons.OK);
            }
            else
            {
                JavaVersion = javaVersion.SelectedItem.ToString();


                config = new Config(From1, to, JavaVersion, zipPath.FileName);
                var lmfao = config.compress();

                MessageBox.Show(lmfao, "epic", MessageBoxButtons.OK);
            }

        }

        private void guna2Button2_Click(object sender, EventArgs e)
            {
                
                this.zipPath = new OpenFileDialog()
                {
                    Title = "Select Zip File",
                    Filter = "zip files (*.zip)|*.zip",
                    DefaultExt = "zip",
                    FilterIndex = 2,
                    RestoreDirectory = true
                };

                if (this.zipPath.ShowDialog() == DialogResult.OK)
                {

                        MessageBox.Show("Successfully stored which file to convert. You can now safely convert.", "Success!", MessageBoxButtons.OK);
                    }
                }

        private void javaVersion_SelectedIndexChanged(object sender, EventArgs e)
        {
         
        }

        private void homepnltxt_Click(object sender, EventArgs e)
        {
            throw new System.NotImplementedException();
        }

        private void convertbtn_Click_1(object sender, EventArgs e)
        {

            Process.Start("run.bat");

        }


        private void from1_SelectedIndexChanged(object sender, EventArgs e)
        {
            this.from1.SelectedText = e.ToString();
        }
    }
         }

    namespace PackConverter
    {
        public class Config
        {
            public string from;
            public string to;
            public string javaVersion;
            public string zipPath;

            public Config(string from1, string to1, string javaVersion1, string zipPath1)

            {

                this.from = from1;
                this.to = to1;
                this.javaVersion = javaVersion1;
                this.zipPath = zipPath1;

            }

            public string compress()
            {
                string e = JsonConvert.SerializeObject(this);
                
                using (StreamWriter sw = new StreamWriter("config.json"))
                {
                    sw.WriteLineAsync(e);
                }

                return e;

            }
        }
    }
